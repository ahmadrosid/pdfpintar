import AuthenticatedLayout from "@/Layouts/authenticated-layout";
import { Head, useForm, router } from "@inertiajs/react";
import { Document, PageProps } from "@/types";
import { Trash2, Upload, XIcon } from "lucide-react";
import { FormEventHandler, useState } from "react";
import Modal from "@/Components/modal";
import UploadTab from "@/Components/upload-tab";
import { Separator } from "@/Components/ui/separator";
import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";

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
                                <Input
                                    type="text"
                                    placeholder="Search document"
                                />
                                <Button onClick={openModal}>
                                    <Upload className="w-4 h-4 mr-2" />
                                    Upload
                                </Button>

                                <Modal show={isOpenUpload} onClose={closeModal}>
                                    <div>
                                        <div className="flex justify-between items-center px-4 py-3">
                                            <h2 className="text-lg font-medium text-gray-900">
                                                Upload PDF document
                                            </h2>
                                            <Button
                                                type="button"
                                                onClick={closeModal}
                                                variant="ghost"
                                                size="sm"
                                            >
                                                <XIcon className="w-4 h-4" />
                                            </Button>
                                        </div>
                                        <Separator />
                                        <div className="py-4 px-6">
                                            <UploadTab />
                                        </div>
                                    </div>
                                </Modal>
                            </div>
                            <Separator />
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
                                                        item.id,
                                                    )}
                                                    className="font-medium hover:underline"
                                                >
                                                    {item.title}
                                                </a>
                                            </p>
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
