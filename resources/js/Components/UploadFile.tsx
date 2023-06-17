import PrimaryButton from "@/Components/PrimaryButton";
import { useForm } from "@inertiajs/react";
import { Progress } from "@/Components/Progress";
import { cn } from "@/lib/utils";
import toast from "react-hot-toast";

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
                        "border border-teal-500 p-2 rounded border-dashed",
                        "block w-full text-sm text-slate-500",
                        "file:mr-4 file:py-2 file:px-4",
                        "file:rounded-md file:border-0",
                        "file:text-sm file:font-semibold",
                        "file:bg-teal-100 file:text-teal-700",
                        "hover:file:bg-teal-200 hover:file:cursor-pointer shadow-none mt-2"
                    )}
                />
            )}

            <div className="flex items-center gap-4 py-2 min-w-[100px]">
                <PrimaryButton
                    onClick={submit}
                    className="px-3 h-10 w-full justify-center"
                >
                    Submit
                </PrimaryButton>
            </div>
        </div>
    );
}
