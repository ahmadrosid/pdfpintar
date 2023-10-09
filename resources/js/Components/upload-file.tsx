import { useForm } from "@inertiajs/react";
import { Progress } from "@/Components/progress";
import { cn } from "@/lib/utils";
import toast from "react-hot-toast";
import { Label } from "./ui/label";
import { Button } from "./ui/button";

export default function UploadFile() {
    const { setData, post, progress } = useForm({
        file: null,
    });

    const submit = () => {
        post(route("documents.store"), {
            onFinish: () => {
                toast.success("Upload finished!");
                setTimeout(() => {
                    window.location.reload();
                }, 800);
            },
        });
    };

    return (
        <div className="">
            {progress ? (
                <Progress className="w-full block" value={progress.percentage}>
                    {progress.percentage}%
                </Progress>
            ) : (
                <div>
                    <Label htmlFor="current_file">Or upload from file</Label>
                    <input
                        id="current_file"
                        onChange={(e) => {
                            if (!e.target.files) return;
                            setData(
                                "file",
                                // @ts-ignore
                                e.target.files[0]
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
                            "hover:file:bg-primary/70 hover:file:cursor-pointer shadow-none mt-2"
                        )}
                    />
                </div>
            )}

            <div className="flex items-center gap-4 py-2 min-w-[100px] pt-4">
                <Button
                    onClick={submit}
                    type="button"
                    className="px-3 h-10 w-full justify-center"
                >
                    Submit
                </Button>
            </div>
        </div>
    );
}
