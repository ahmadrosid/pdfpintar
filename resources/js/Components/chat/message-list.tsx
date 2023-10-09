import React from "react";
import Markdown from "react-markdown";
import clsx from "clsx";
import "@react-pdf-viewer/default-layout/lib/styles/index.css";
import "@react-pdf-viewer/core/lib/styles/index.css";
import { Bot, User } from "lucide-react";

export type Message = {
    content: string;
    role: string;
    metadata?: {
        page: number;
    }[];
};

type MessageListProps = {
    messages: Message[];
};

export const MessageList = React.memo(({ messages }: MessageListProps) => {
    return (
        <>
            {messages.map((item, idx) => (
                <div
                    className={clsx(
                        "p-6 flex gap-4 items-start border-b border-gray-100",
                        item.role === "assistant" ? "bg-gray-50" : "bg-white"
                    )}
                    key={idx}
                >
                    {item.role === "user" ? (
                        <div className="w-[28px] flex justify-center items-center bg-stone-500 rounded p-1 text-white">
                            <User className="w-5 h-5" />
                        </div>
                    ) : (
                        <div className="w-[28px] flex justify-center items-center bg-primary rounded p-1 text-white">
                            <Bot className="w-5 h-5" />
                        </div>
                    )}
                    <Markdown className="flex-1 prose">{item.content}</Markdown>
                </div>
            ))}
        </>
    );
});
