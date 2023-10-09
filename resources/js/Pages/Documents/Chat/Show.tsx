import "@react-pdf-viewer/default-layout/lib/styles/index.css";
import "@react-pdf-viewer/core/lib/styles/index.css";
import { Head, Link, useForm } from "@inertiajs/react";
import { Chat, Document, PageProps } from "@/types";
import { ArrowLeft, Send } from "lucide-react";
import { FormEventHandler, useState } from "react";
import LoadingDots from "@/Components/LoadingDots";
import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";
import { MessageList, type Message } from "@/Components/chat/message-list";
import { StreamingMessage } from "@/Components/chat/streaming-message";
import { SampleQuestions } from "@/Components/chat/sample-question";
import { DocumentDisplay } from "@/Components/chat/document-display";

type DocumentIndexProps = PageProps<{
    document?: Document;
    message?: Message[];
    chat: Chat;
}>;

export default function DocumentIndex({
    chat,
    document,
    message,
}: DocumentIndexProps) {
    const [messages, setMessage] = useState<Message[]>(message || []);
    const [isShowStreaming, setShowStreaming] = useState(false);
    const [streamText, setStreamingText] = useState("");

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
        let sseText = "";
        source.addEventListener("update", (event) => {
            if (event.data === "<END_STREAMING_SSE>") {
                source.close();
                setMessage((prev) => {
                    return [
                        ...prev,
                        {
                            metadata: [],
                            role: "bot",
                            content: sseText,
                        },
                    ];
                });
                setShowStreaming(false);
                setStreamingText("");
                return;
            }
            const data = JSON.parse(event.data);
            if (data.text) {
                sseText += data.text;
                setStreamingText((prev) => prev + data.text);
            }
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
    };

    const handlePromptButton = (text: string) => {
        setMessage((prev) => {
            return [
                ...prev,
                {
                    content: text,
                    role: "user",
                },
            ];
        });
        triggerStreaming(text);
    };

    return (
        <>
            <Head title="Documents" />
            <div>
                <div className="bg-white overflow-hidden">
                    <div className="flex justify-between items-center h-[7vh] bg-white">
                        <div className="px-4 flex items-center gap-2">
                            <Link href={route("documents.index")}>
                                <Button variant={"ghost"} size="sm">
                                    <ArrowLeft className="w-5 h-5" />
                                </Button>
                            </Link>

                            <h2 className="font-medium">{document?.title}</h2>
                        </div>
                        <div className="px-6">
                            <h2 className="">Share</h2>
                        </div>
                    </div>
                    <div className="grid grid-cols-2">
                        {document && <DocumentDisplay document={document} />}
                        <div className="flex flex-col">
                            <div className="flex-1 border-t border-gray-200">
                                <div className="h-[83vh] overflow-auto">
                                    <MessageList messages={messages} />
                                    {messages.length === 0 && (
                                        <SampleQuestions
                                            handlePromptButton={
                                                handlePromptButton
                                            }
                                        />
                                    )}
                                    <StreamingMessage
                                        show={isShowStreaming}
                                        text={streamText}
                                    />
                                </div>
                            </div>
                            <div className="p-4 bg-gray-50 border-t">
                                <form
                                    onSubmit={handleSubmitChat}
                                    className="flex items-center relative"
                                >
                                    <Input
                                        type="text"
                                        placeholder="Type your message here"
                                        value={data.question}
                                        onChange={(e) =>
                                            setData("question", e.target.value)
                                        }
                                    />
                                    <button
                                        disabled={processing}
                                        className="absolute rounded-md right-1 text-sm p-2 hover:bg-teal-50"
                                    >
                                        {isShowStreaming ? (
                                            <LoadingDots size="large" />
                                        ) : (
                                            <Send className="w-4 h-4" />
                                        )}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
