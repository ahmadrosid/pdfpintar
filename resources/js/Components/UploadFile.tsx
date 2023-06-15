import { useRef, FormEventHandler } from "react";
import PrimaryButton from "@/Components/PrimaryButton";
import { useForm } from "@inertiajs/react";
import { Progress } from "@/Components/Progress";
import { cn } from "@/lib/utils";

export default function UploadFile() {
    const currentFileInput = useRef<HTMLInputElement | null>(null);
    const { setData, post, reset, progress } = useForm({
        file: null,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route("documents.store"), {
            preserveState: true,
            onSuccess: () => reset(),
        });
    };

    return (
        <form onSubmit={submit} className="">
            <div className="">
                {progress ? (
                    <Progress
                        className="w-full block"
                        value={progress.percentage}
                    >
                        {progress.percentage}%
                    </Progress>
                ) : (
                    <input
                        id="current_file"
                        ref={currentFileInput}
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
                        className={cn(
                            "border border-teal-700 p-2 rounded border-dashed",
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
                    <PrimaryButton className="px-3 h-10 w-full justify-center">
                        Submit
                    </PrimaryButton>
                </div>
            </div>
        </form>
    );
}
