import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link } from "@inertiajs/react";
import { Document, PageProps } from "@/types";
import { Upload } from "lucide-react";

export default function DocumentIndex({
    auth,
    documents,
}: PageProps<{ documents?: Document[] }>) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    Documents
                </h2>
            }
        >
            <Head title="Documents" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 space-y-6 min-h-[25vh]">
                            <div className="flex w-full items-center gap-4">
                                <input
                                    type="text"
                                    className="flex-1 p-2 px-4 rounded-md border-violet-200 focus:ring-0 active:ring-0 focus:border-violet-200"
                                    placeholder="Search document"
                                />
                                <Link
                                    href="/documents/create"
                                    className="block"
                                >
                                    <button className="bg-violet-500 rounded-md text-white h-10 px-4 inline-flex items-center w-fit">
                                        <Upload className="w-4 h-4 mr-3" />
                                        Upload
                                    </button>
                                </Link>
                            </div>
                            <div>
                                {documents?.map((item, idx) => (
                                    <div key={idx}>
                                        <p className="py-2">
                                            <a
                                                href={route(
                                                    "documents.show",
                                                    item.id
                                                )}
                                                className="font-medium hover:underline"
                                            >
                                                {item.title}
                                            </a>
                                        </p>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
