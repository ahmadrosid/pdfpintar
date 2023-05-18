import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head } from "@inertiajs/react";
import { PageProps } from "@/types";
import { useRef, FormEventHandler } from "react";
import PrimaryButton from "@/Components/PrimaryButton";
import { useForm } from "@inertiajs/react";
import InputError from "@/Components/InputError";
import { Progress } from "@/Components/Progress";
import { cn } from "@/lib/utils";

export default function DocumentCreate({
    auth,
    path,
    errors,
}: PageProps<{ path?: string; errors: any }>) {
    const currentFileInput = useRef<HTMLInputElement | null>(null);
    const { data, setData, post, reset, progress } = useForm({
        file: null,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route("documents.store"), { onSuccess: () => reset() });
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    Document Upload
                </h2>
            }
        >
            <Head title="Document Upload" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="flex items-center justify-center text-gray-900 min-h-[25vh]">
                            <form
                                onSubmit={submit}
                                className="max-w-xl mx-auto"
                            >
                                {path && (
                                    <p className="py-4">
                                        <a
                                            href={path}
                                            target="_blank"
                                            className="underline text-teal-500"
                                        >
                                            Succesfully upload
                                        </a>
                                    </p>
                                )}

                                <div className="flex gap-2 justify-between items-center">
                                    {progress ? (
                                        <Progress value={progress.percentage}>
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
                                                "block w-full text-sm text-slate-500",
                                                "file:mr-4 file:py-2 file:px-4",
                                                "file:rounded-full file:border-0",
                                                "file:text-sm file:font-semibold",
                                                "file:bg-teal-50 file:text-teal-700",
                                                "hover:file:bg-teal-100 shadow-none mt-2"
                                            )}
                                        />
                                    )}

                                    <div className="flex items-center gap-4">
                                        <PrimaryButton>Upload</PrimaryButton>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
