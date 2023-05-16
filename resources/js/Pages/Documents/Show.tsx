import { Head, Link, useForm } from "@inertiajs/react";
import { Document, PageProps } from "@/types";
import { Worker, Viewer, ProgressBar } from "@react-pdf-viewer/core";
import { defaultLayoutPlugin } from "@react-pdf-viewer/default-layout";
import { ArrowLeft, Bot, User } from "lucide-react";
import clsx from "clsx";

import "@react-pdf-viewer/default-layout/lib/styles/index.css";
import "../../../css/default_layout_theme.css";
import "@react-pdf-viewer/core/lib/styles/index.css";
import { FormEventHandler, useState } from "react";
import { router } from "@inertiajs/react";

type ChatMetadata = {
    page: number;
};

type Chat = {
    message: string;
    role: string;
    metadata?: ChatMetadata[];
};

type DocumentIndexProps = PageProps<{ document?: Document; chat?: Chat }>;

export default function DocumentIndex({ document }: DocumentIndexProps) {
    const defaultLayoutPluginInstance = defaultLayoutPlugin();
    const [messages, setMessage] = useState<Chat[]>([]);

    const { data, setData, reset } = useForm({
        question: "",
        document_id: document?.id,
    });

    const handleSubmitChat: FormEventHandler = async (e) => {
        e.preventDefault();
        setMessage((prev) => {
            return [
                ...prev,
                {
                    message: data.question,
                    role: "user",
                },
            ];
        });
        router.post(route("documents.search"), data, {
            onSuccess: (response) => {
                const props = response.props as any;
                setMessage((prev) => {
                    return [...prev, props.chat];
                });
                reset();
            },
        });
    };

    return (
        <>
            <Head title="Documents"></Head>
            <div>
                <div className="bg-white overflow-hidden">
                    <div className="flex justify-between items-center h-[7vh] bg-violet-100">
                        <div className="px-4">
                            <Link href={route("documents.index")}>
                                <button className="h-9 px-2 hover:bg-violet-200 rounded-md inline-flex gap-2 items-center text-base">
                                    <ArrowLeft className="w-5 h-5" />
                                    Go Back
                                </button>
                            </Link>
                        </div>
                        <div className="px-4">
                            <h2 className="font-medium">{document?.title}</h2>
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
                            <div className="flex-1 border-t border-violet-200">
                                <div>
                                    {messages.map((item, idx) => (
                                        <div
                                            className={clsx(
                                                "p-4 flex gap-2 items-start border-b border-violet-200",
                                                item.role === "bot"
                                                    ? "bg-violet-100"
                                                    : "bg-white"
                                            )}
                                            key={idx}
                                        >
                                            {item.role === "user" ? (
                                                <div className="w-[33px] flex justify-center items-center bg-stone-600 rounded-md p-1.5 text-white">
                                                    <User className="w-5 h-5" />
                                                </div>
                                            ) : (
                                                <div className="w-[33px] flex justify-center items-center bg-violet-600 rounded-md p-1.5 text-white">
                                                    <Bot className="w-5 h-5" />
                                                </div>
                                            )}
                                            <p className="flex-1">
                                                {item.message}
                                            </p>
                                        </div>
                                    ))}
                                </div>
                            </div>
                            <div className="p-4 bg-violet-100">
                                <form
                                    onSubmit={handleSubmitChat}
                                    className="flex gap-2"
                                >
                                    <input
                                        type="text"
                                        placeholder="Type your message here"
                                        onChange={(e) =>
                                            setData("question", e.target.value)
                                        }
                                        className="p-2 w-full rounded outline-none border-violet-200 focus:ring-0 active:ring-0 focus:border-violet-300"
                                    />
                                    <button className="text-sm">Send</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
