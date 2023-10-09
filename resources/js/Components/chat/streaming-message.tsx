import Markdown from "react-markdown";
import { Bot } from "lucide-react";
import clsx from "clsx";

export const StreamingMessage = ({
    show,
    text,
}: {
    show: boolean;
    text: string;
}) => (
    <div
        className={clsx(
            !show && "hidden",
            "p-6 flex gap-4 items-start bg-muted"
        )}
    >
        <div className="w-[28px] flex justify-center items-center bg-primary rounded-md p-1 text-white">
            <Bot className="w-5 h-5" />
        </div>
        <div className="flex-1">
            <Markdown className="flex-1 prose">{text}</Markdown>
            <span className="inline-block w-1.5 h-4 bg-muted-foreground animate-blink"></span>
        </div>
    </div>
);
