import { defaultLayoutPlugin } from "@react-pdf-viewer/default-layout";
import { Worker, Viewer, ProgressBar } from "@react-pdf-viewer/core";
import { Document } from "@/types";
import React from "react";

export const DocumentDisplay = React.memo(
    ({ document }: { document: Document }) => {
        const defaultLayoutPluginInstance = defaultLayoutPlugin();

        return (
            <>
                <div className="max-h-[93vh]">
                    <Worker workerUrl="https://unpkg.com/pdfjs-dist@3.6.172/build/pdf.worker.min.js">
                        <Viewer
                            fileUrl={document.path}
                            plugins={[defaultLayoutPluginInstance]}
                            renderLoader={(percentages: number) => (
                                <div style={{ width: "240px" }}>
                                    <ProgressBar
                                        progress={Math.round(percentages)}
                                    />
                                </div>
                            )}
                        />
                    </Worker>
                </div>

                <style>{`:root {
    --rpv-default-layout__body-background-color: #fff;

    --rpv-default-layout__container-border-color: rgb(229 231 235 / 1);

    --rpv-default-layout__toolbar-background-color: #fff;
    --rpv-default-layout__toolbar-border-bottom-color: rgb(229 231 235 / 1);

    --rpv-default-layout__sidebar-border-color: rgb(229 231 235 / 1);
    --rpv-default-layout__sidebar--opened-background-color: #fff;
    --rpv-default-layout__sidebar-headers-background-color: #fff;
    --rpv-default-layout__sidebar-content--opened-background-color: #fff;
    --rpv-default-layout__sidebar-content--opened-border-color: rgb(229 231 235 / 1);
    --rpv-default-layout__sidebar-content--opened-color: #000;
}`}</style>
            </>
        );
    }
);
