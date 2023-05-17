import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, router, useForm } from "@inertiajs/react";
import { Chat, PageProps } from "@/types";
import { Trash2 } from "lucide-react";
import { FormEventHandler } from "react";

export default function DocumentIndex({
    auth,
    chats,
}: PageProps<{ chats?: Chat[] }>) {
    const { reset } = useForm();
    const handleDeleteChat: FormEventHandler = (e) => {
        e.preventDefault();
        // @ts-expect-error
        router.delete(route("chat.destroy", e.currentTarget.chat_id.value), {
            onSuccess: () => reset(),
        });
    };
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    Chat
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
                                    className="flex-1 p-2 px-4 rounded-md border-teal-200 focus:ring-0 active:ring-0 focus:border-teal-200"
                                    placeholder="Search chat"
                                />
                                <Link
                                    href="/documents/create"
                                    className="block"
                                >
                                    <button className="bg-teal-500 hover:bg-teal-600 rounded-md text-white h-10 px-3 inline-flex items-center w-fit">
                                        <Trash2 className="w-4 h-4 mr-2" />
                                        Clear All
                                    </button>
                                </Link>
                            </div>
                            <div>
                                {chats?.map((item, idx) => (
                                    <div
                                        key={idx}
                                        className="flex justify-between items-center"
                                    >
                                        <p className="py-2">
                                            <a
                                                href={route(
                                                    "chat.show",
                                                    item.id
                                                )}
                                                className="font-medium hover:underline"
                                            >
                                                {item.title}
                                            </a>
                                        </p>
                                        <div className="flex items-center gap-4">
                                            <p>{item.created_at}</p>
                                            <form onSubmit={handleDeleteChat}>
                                                <input
                                                    type="hidden"
                                                    name="chat_id"
                                                    value={item.id}
                                                />
                                                <button>
                                                    <Trash2 />
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
