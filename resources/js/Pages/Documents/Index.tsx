import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link } from "@inertiajs/react";
import { Document, PageProps } from "@/types";
import { Trash, Trash2, Upload } from "lucide-react";

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
                        <div className="p-6 space-y-6">
                            <div className="flex w-full items-center gap-4">
                                <input
                                    type="text"
                                    className="flex-1 p-2 px-4 rounded-md border-green-300 focus:ring-0 active:ring-0 focus:border-green-300"
                                    placeholder="Search document"
                                />
                                <Link
                                    href="/documents/create"
                                    className="block"
                                >
                                    <button className="bg-green-500 hover:bg-green-600 rounded-md text-white h-10 px-3 inline-flex items-center w-fit">
                                        <Upload className="w-4 h-4 mr-2" />
                                        Upload
                                    </button>
                                </Link>
                            </div>
                            <div>
                                {documents?.map((item, idx) => (
                                    <div
                                        key={idx}
                                        className="flex justify-between items-center"
                                    >
                                        <p className="py-2">
                                            <a
                                                href={route(
                                                    "documents.update",
                                                    item.id
                                                )}
                                                className="font-medium hover:underline"
                                            >
                                                {item.title}
                                            </a>
                                        </p>
                                        <div className="flex items-center gap-4">
                                            <p>{item.created_at}</p>
                                            <button className="hover:bg-green-100 rounded-md">
                                                <Trash2 />
                                            </button>
                                        </div>
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
