import AuthenticatedLayout from "@/Layouts/authenticated-layout";
import { Head, Link, router, useForm } from "@inertiajs/react";
import { Chat, PageProps } from "@/types";
import { Trash2 } from "lucide-react";
import { FormEventHandler } from "react";
import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";
import { Separator } from "@/Components/ui/separator";

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
                            <div className="flex items-center gap-4">
                                <Input
                                    type="text"
                                    placeholder="Search chat"
                                    className="flex-1"
                                />
                                <Link
                                    href="/documents/create"
                                    className="block"
                                >
                                    <Button className="justify-between w-full">
                                        <Trash2 className="w-4 h-4 mr-2" />
                                        <span className="text-sm">
                                            Clear All
                                        </span>
                                    </Button>
                                </Link>
                            </div>
                            <Separator />
                            <div className="grid gap-2">
                                {chats?.map((item, idx) => (
                                    <div
                                        key={idx}
                                        className="flex justify-between items-center"
                                    >
                                        <p className="py-2">
                                            <a
                                                href={route(
                                                    "chat.show",
                                                    item.id,
                                                )}
                                                className="font-medium hover:underline"
                                            >
                                                {item.title}
                                            </a>
                                        </p>
                                        <div className="flex items-center gap-4">
                                            <p className="text-sm">
                                                {item.created_at}
                                            </p>
                                            <form onSubmit={handleDeleteChat}>
                                                <input
                                                    type="hidden"
                                                    name="chat_id"
                                                    value={item.id}
                                                />
                                                <Button
                                                    size="sm"
                                                    variant="ghost"
                                                >
                                                    <Trash2 className="w-4 h-4" />
                                                </Button>
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
