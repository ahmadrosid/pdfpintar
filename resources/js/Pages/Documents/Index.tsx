import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, useForm, router } from "@inertiajs/react";
import { Document, PageProps } from "@/types";
import { Trash2, Upload } from "lucide-react";
import { FormEventHandler, useEffect, useState } from "react";
import { Badge } from "@/Components/Badge";
import Modal from "@/Components/Modal";
import UploadTab from "@/Components/UploadTab";

export default function DocumentIndex({
    auth,
    documents,
}: PageProps<{ documents?: Document[] }>) {
    const { reset } = useForm();
    const [isOpenUpload, setIsOpenUpload] = useState(false);

    const openModal = () => setIsOpenUpload(true);
    const closeModal = () => setIsOpenUpload(false);

    const handleDeleteDocument: FormEventHandler = (e) => {
        e.preventDefault();
        // @ts-expect-error
        router.delete(route("documents.destroy", e.currentTarget.id.value), {
            onSuccess: () => reset(),
        });
    };

    const refetchDocuments = () => {
        setTimeout(() => {
            window.location.reload();
        }, 1500);
    };

    useEffect(() => {
        if (!documents) return;
        if (documents.length > 0) {
            const isAllComplete = documents.every(
                (item) => item.status === "complete"
            );
            if (!isAllComplete) {
                refetchDocuments();
            }
        }
    }, []);

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
                                    className="flex-1 p-2 px-4 rounded-md border-teal-300 focus:ring-0 active:ring-0 focus:border-teal-300"
                                    placeholder="Search document"
                                />
                                {/* <Link
                                    href="/documents/create"
                                    className="block"
                                > */}
                                <button
                                    onClick={openModal}
                                    className="bg-teal-500 hover:bg-teal-600 rounded-md text-white h-10 px-3 inline-flex items-center w-fit"
                                >
                                    <Upload className="w-4 h-4 mr-2" />
                                    Upload
                                </button>

                                <Modal show={isOpenUpload} onClose={closeModal}>
                                    <form
                                        onSubmit={() => false}
                                        className="p-6"
                                    >
                                        <h2 className="text-lg font-medium text-gray-900">
                                            Upload PDF document
                                        </h2>

                                        <UploadTab />
                                    </form>
                                </Modal>
                                {/* </Link> */}
                            </div>
                            <div>
                                {documents?.map((item, idx) => (
                                    <div
                                        key={idx}
                                        className="flex justify-between items-center"
                                    >
                                        <div className="flex items-center gap-2">
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
                                            {item.status &&
                                                item.status !== "complete" && (
                                                    <Badge
                                                        variant={"secondary"}
                                                        className="bg-teal-400 text-white font-normal animate-pulse"
                                                    >
                                                        {item.status}
                                                    </Badge>
                                                )}
                                        </div>
                                        <div className="flex items-center gap-4">
                                            <p className="text-gray-500 text-sm">
                                                {item.created_at}
                                            </p>
                                            <form
                                                className="flex items-center"
                                                onSubmit={handleDeleteDocument}
                                            >
                                                <input
                                                    type="hidden"
                                                    value={item.id}
                                                    name="id"
                                                />
                                                <button>
                                                    <Trash2 className="w-5 h-5" />
                                                </button>
                                            </form>
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
