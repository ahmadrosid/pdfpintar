import { toast } from "react-hot-toast";
import { useForm } from "@inertiajs/react";
import LoadingDots from "./loading-dots";
import { Input } from "@/Components/ui/input";
import { Label } from "@/Components/ui/label";
import { Separator } from "./ui/separator";
import { cn } from "@/lib/utils";
import { Button } from "./ui/button";

export default function UploadTab() {
    const { data, setData, post, processing, progress, errors } = useForm({
        pdfUrl: "",
        file: null,
    });

    const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        if (data.pdfUrl !== "") {
            submitURL();
            return;
        }

        if (data.file !== null) {
            submitFile();
            return;
        }

        toast.error(
            "Please provide either url of the document or upload one from your device!",
        );
        return;
    };

    const submitURL = () => {
        post(route("documents.include"), {
            onFinish: () => {
                toast.success("Upload finished!");
                setTimeout(() => {
                    window.location.reload();
                }, 800);
            },
            onError: () => {
                toast.error("Error uploading file");
            },
        });
    };

    const submitFile = () => {
        post(route("documents.store"), {
            onError: () => {
                toast.error("Error uploading file");
            },
            onFinish: () => {
                toast.success("Upload finished!");
                setTimeout(() => {
                    window.location.reload();
                }, 800);
            },
        });
    };

    return (
        <div className="pt-4">
            <form onSubmit={handleSubmit} className="pb-4">
                <div className="space-y-2">
                    <Label htmlFor="url_pdf">Upload from url</Label>
                    <Input
                        id="url_pdf"
                        onChange={(e) => setData("pdfUrl", e.target.value)}
                        placeholder="https://example.com/file.pdf"
                        className="w-full p-2"
                    />
                </div>
                <Separator className="my-4" />
                <div className="">
                    <div>
                        <Label htmlFor="current_file">
                            Or upload from file
                        </Label>
                        <input
                            id="current_file"
                            onChange={(e) => {
                                if (!e.target.files) return;
                                setData(
                                    "file",
                                    // @ts-ignore
                                    e.target.files[0],
                                );
                            }}
                            type="file"
                            name="file"
                            accept="application/pdf"
                            className={cn(
                                "border border-primary p-2 rounded border-dashed",
                                "block w-full text-sm text-slate-500",
                                "file:mr-4 file:py-2 file:px-4",
                                "file:rounded-md file:border-0",
                                "file:text-sm file:font-semibold",
                                "file:bg-primary file:text-primary-foreground",
                                "hover:file:bg-primary/70 hover:file:cursor-pointer shadow-none mt-2",
                            )}
                        />
                    </div>

                    <div className="flex items-center gap-4 py-2 min-w-[100px] pt-4">
                        <Button
                            type="submit"
                            disabled={processing}
                            className="px-3 h-10 w-full justify-center"
                        >
                            {processing ? (
                                <LoadingDots color="white" />
                            ) : (
                                "Submit"
                            )}
                        </Button>
                    </div>
                </div>
            </form>
        </div>
    );
}
