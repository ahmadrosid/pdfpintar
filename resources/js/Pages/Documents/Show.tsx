import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link } from "@inertiajs/react";
import { Document, PageProps } from "@/types";
import { Worker, Viewer, ProgressBar } from "@react-pdf-viewer/core";
import { defaultLayoutPlugin } from "@react-pdf-viewer/default-layout";
import { ArrowLeft } from "lucide-react";

import "@react-pdf-viewer/default-layout/lib/styles/index.css";
import "@react-pdf-viewer/core/lib/styles/index.css";

export default function DocumentIndex({
    auth,
    document,
}: PageProps<{ document?: Document }>) {
    const defaultLayoutPluginInstance = defaultLayoutPlugin();

    return (
        <>
            <Head title="Documents" />

            <div>
                <div className="bg-white overflow-hidden">
                    <div className="flex justify-between items-center h-[7vh] bg-violet-100">
                        <div className="px-4">
                            <Link href={route("documents.index")}>
                                <button className="h-9 px-4 hover:bg-violet-200 rounded-md inline-flex gap-2 items-center text-base">
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
                            <div className="flex-1 border-t border-gray-400">
                                <div className="p-4">Message body</div>
                            </div>
                            <div className="p-4 bg-violet-100">
                                <div className="flex gap-2">
                                    <input
                                        type="text"
                                        placeholder="Type your message here"
                                        className="p-2 w-full rounded outline-none border-violet-200"
                                    />
                                    <button>Send</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
