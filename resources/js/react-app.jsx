import React from "react";
import { createRoot } from "react-dom/client";
import { Worker, Viewer, ProgressBar } from "@react-pdf-viewer/core";
import { defaultLayoutPlugin } from "@react-pdf-viewer/default-layout";
import "@react-pdf-viewer/core/lib/styles/index.css";
import "@react-pdf-viewer/default-layout/lib/styles/index.css";
import jumpToPagePlugin from "./jump-to-page-plugin";

const container = document.getElementById("pdf-viewer");

function PDFView() {
    const defaultLayoutPluginInstance = defaultLayoutPlugin({
        toolbarPlugin: {
            downloadButton: {
                hidden: true,
            },
        },
    });
    const jumpPluginInstance = jumpToPagePlugin();

    React.useEffect(() => {

        window.addEventListener('jumpToPage', (e) => {
            const pageIndex = e.detail.pageIndex;
            console.log(pageIndex);
            jumpPluginInstance.jumpToPage(pageIndex > 0 ? pageIndex - 1 : 0);
        });

        return () => {
            window.removeEventListener('jumpToPage');
        };
    }, []);

    return (
        <>
            <Worker workerUrl="https://unpkg.com/pdfjs-dist@3.4.120/build/pdf.worker.min.js">
                <div 
                    style={{
                        height: '92vh',
                    }}>
                    <Viewer
                        theme={window.matchMedia('(prefers-color-scheme: dark)').matches ? "dark" : "light"}
                        fileUrl={container.dataset.url}
                        plugins={[defaultLayoutPluginInstance,jumpPluginInstance]}
                        renderLoader={(percentages) => (
                            <div style={{ width: "240px" }}>
                                <ProgressBar progress={Math.round(percentages)} />
                            </div>
                        )}
                    />
                </div>
            </Worker>
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

if (container) {
    const root = createRoot(container);
    root.render(<PDFView />);
}
