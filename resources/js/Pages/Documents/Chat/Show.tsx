import { Head, Link, useForm } from "@inertiajs/react";
import { Chat, Document, PageProps } from "@/types";
import { Worker, Viewer, ProgressBar } from "@react-pdf-viewer/core";
import { defaultLayoutPlugin } from "@react-pdf-viewer/default-layout";
import { ArrowLeft, Bot, User, Send } from "lucide-react";
import clsx from "clsx";

import "@react-pdf-viewer/default-layout/lib/styles/index.css";
import "@react-pdf-viewer/core/lib/styles/index.css";
import {
    ElementRef,
    FormEventHandler,
    forwardRef,
    useRef,
    useState,
} from "react";
import { router } from "@inertiajs/react";

type Metadata = {
    page: number;
};

type Message = {
    content: string;
    role: string;
    metadata?: Metadata[];
};

type DocumentIndexProps = PageProps<{
    document?: Document;
    message?: Message;
    chat: Chat;
}>;

type StreamingMessageProps = {
    show: boolean;
};

const StreamingMessage = forwardRef<ElementRef<"p">, StreamingMessageProps>(
    ({ show }, ref) => (
        <div
            className={clsx(
                !show && "hidden",
                "p-6 flex gap-4 items-start border-b border-teal-100 bg-teal-50"
            )}
        >
            <div className="w-[28px] flex justify-center items-center bg-teal-500 rounded-md p-1 text-white">
                <Bot className="w-5 h-5" />
            </div>
            <p ref={ref} className="flex-1 text-base"></p>
        </div>
    )
);

export default function DocumentIndex({ chat, document }: DocumentIndexProps) {
    const defaultLayoutPluginInstance = defaultLayoutPlugin();
    const [messages, setMessage] = useState<Message[]>([]);
    const [isShowStreaming, setShowStreaming] = useState(false);
    const resultRef = useRef<HTMLParagraphElement | null>(null);

    const { data, setData, processing } = useForm({
        question: "",
    });

    const triggerStreaming = (question: string) => {
        const queryQuestion = encodeURIComponent(question);
        const source = new EventSource(
            `${route("chat.streaming")}?question=${queryQuestion}&chat_id=${
                chat.id
            }`
        );
        setShowStreaming(true);
        source.addEventListener("update", (event) => {
            if (event.data === "<END_STREAMING_SSE>") {
                source.close();
                setMessage((prev) => {
                    return [
                        ...prev,
                        {
                            metadata: [],
                            role: "bot",
                            content: resultRef.current?.innerText!!,
                        },
                    ];
                });
                setShowStreaming(false);
                // @ts-expect-error
                resultRef.current.innerText = "";
                return;
            }
            // @ts-expect-error
            resultRef.current.innerText += event.data;
        });
    };

    const handleSubmitChat: FormEventHandler = async (e) => {
        e.preventDefault();
        setMessage((prev) => {
            return [
                ...prev,
                {
                    content: data.question,
                    role: "user",
                },
            ];
        });
        triggerStreaming(data.question);
        setData("question", "");
        // router.put(route("chat.update", chat.id), data, {
        //     onSuccess: (response) => {
        //         const props = response.props as any;
        //         setMessage((prev) => {
        //             return [...prev, props.message];
        //         });
        //     },
        // });
    };

    return (
        <>
            <Head title="Documents"></Head>
            <div>
                <div className="bg-white overflow-hidden">
                    <div className="flex justify-between items-center h-[7vh] bg-teal-50">
                        <div className="px-4 flex items-center gap-2">
                            <Link href={route("documents.index")}>
                                <button className="h-9 px-2 hover:bg-teal-200 rounded-md inline-flex gap-2 items-center text-base">
                                    <ArrowLeft className="w-5 h-5" />
                                </button>
                            </Link>

                            <h2 className="font-medium">{document?.title}</h2>
                        </div>
                        <div className="px-6">
                            <h2 className="">Share</h2>
                        </div>
                    </div>
                    <div className="grid grid-cols-2">
                        {document && (
                            <div className="max-h-[93vh]">
                                <Worker workerUrl="https://unpkg.com/pdfjs-dist@3.6.172/build/pdf.worker.min.js">
                                    <Viewer
                                        fileUrl={document.path}
                                        plugins={[defaultLayoutPluginInstance]}
                                        renderLoader={(percentages: number) => (
                                            <div style={{ width: "240px" }}>
                                                <ProgressBar
                                                    progress={Math.round(
                                                        percentages
                                                    )}
                                                />
                                            </div>
                                        )}
                                    />
                                </Worker>
                            </div>
                        )}
                        <div className="flex flex-col">
                            <div className="flex-1 border-t border-teal-200">
                                <div className="h-[84vh] overflow-auto">
                                    {messages.map((item, idx) => (
                                        <div
                                            className={clsx(
                                                "p-6 flex gap-4 items-start border-b border-teal-100",
                                                item.role === "bot"
                                                    ? "bg-teal-50"
                                                    : "bg-white"
                                            )}
                                            key={idx}
                                        >
                                            {item.role === "user" ? (
                                                <div className="w-[28px] flex justify-center items-center bg-stone-500 rounded-md p-1 text-white">
                                                    <User className="w-5 h-5" />
                                                </div>
                                            ) : (
                                                <div className="w-[28px] flex justify-center items-center bg-teal-500 rounded-md p-1 text-white">
                                                    <Bot className="w-5 h-5" />
                                                </div>
                                            )}
                                            <p className="flex-1 text-base">
                                                {item.content}
                                            </p>
                                        </div>
                                    ))}
                                    <StreamingMessage
                                        show={isShowStreaming}
                                        ref={resultRef}
                                    />
                                </div>
                            </div>
                            <div className="p-4 bg-teal-50">
                                <form
                                    onSubmit={handleSubmitChat}
                                    className="flex items-center relative"
                                >
                                    <input
                                        type="text"
                                        placeholder="Type your message here"
                                        value={data.question}
                                        onChange={(e) =>
                                            setData("question", e.target.value)
                                        }
                                        className="p-2 w-full rounded outline-none border-teal-200 focus:ring-0 active:ring-0 focus:border-teal-300"
                                    />
                                    <button
                                        disabled={processing}
                                        className="absolute rounded-md right-1 text-sm p-2 hover:bg-teal-50"
                                    >
                                        <Send className="w-4 h-4" />
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <style>{`
:root {
    --rpv-default-layout__body-background-color: #fff;

    --rpv-default-layout__container-border-color: #99f6e4;

    --rpv-default-layout__toolbar-background-color: #f0fdfa;
    --rpv-default-layout__toolbar-border-bottom-color: #99f6e4;

    --rpv-default-layout__sidebar-border-color: rgba(0, 0, 0, 0.2);
    --rpv-default-layout__sidebar--opened-background-color: #fff;
    --rpv-default-layout__sidebar-headers-background-color: #f0fdfa;
    --rpv-default-layout__sidebar-content--opened-background-color: #fff;
    --rpv-default-layout__sidebar-content--opened-border-color: rgba(0, 0, 0, 0.2);
    --rpv-default-layout__sidebar-content--opened-color: #000;
}`}</style>
        </>
    );
}
