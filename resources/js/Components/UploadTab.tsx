import { cn } from "@/lib/utils";
import { Tab } from "@headlessui/react";
import UploadFile from "./UploadFile";
import TextInput from "./TextInput";
import PrimaryButton from "./PrimaryButton";
import { toast } from "react-hot-toast";
import { useForm } from "@inertiajs/react";
import LoadingDots from "./LoadingDots";

export default function UploadTab() {
    const titles = ["Upload File", "From URL"];

    const { data, setData, post, processing, errors } = useForm({
        pdfUrl: "",
    });

    const handleSubmitUrl = (e: any) => {
        e.preventDefault();
        if (data.pdfUrl === "") {
            toast.error("Please enter a valid url");
            return;
        }
        post(route("documents.include"), {
            onFinish: () => {
                // toast.success("Uploading...\n" + data.pdfUrl);
            },
            onError: () => {
                toast.error("Error uploading file");
            },
            onSuccess: () => {
                toast.success("Upload finished!");
                setTimeout(() => {
                    window.location.reload();
                }, 800);
            },
        });
    };

    return (
        <div className="pt-4">
            <Tab.Group>
                <Tab.List className="flex space-x-1 rounded-xl bg-teal-950/20 p-1">
                    {titles.map((item) => (
                        <Tab
                            key={item}
                            className={({ selected }) =>
                                cn(
                                    "w-full rounded-lg py-2.5 text-sm font-medium leading-5 text-gray-700",
                                    "ring-white ring-opacity-60 ring-offset-2 ring-offset-teal-400 focus:outline-none focus:ring-2",
                                    selected
                                        ? "bg-white shadow"
                                        : "text-gray-600 hover:bg-white/[0.12] hover:text-gray-800"
                                )
                            }
                        >
                            {item}
                        </Tab>
                    ))}
                </Tab.List>
                <Tab.Panels className="mt-2">
                    <Tab.Panel
                        className={cn(
                            "rounded-xl bg-white px-3 pt-3",
                            "ring-white ring-opacity-60 ring-offset-2 ring-offset-teal-400 focus:outline-none focus:ring-2"
                        )}
                    >
                        <UploadFile />
                    </Tab.Panel>
                    <Tab.Panel
                        className={cn(
                            "rounded-xl bg-white p-3",
                            "ring-white ring-opacity-60 ring-offset-2 ring-offset-teal-400 focus:outline-none focus:ring-2"
                        )}
                    >
                        <form className="space-y-4">
                            <div className="space-y-1">
                                <label
                                    className="block text-sm font-medium text-gray-700"
                                    htmlFor="url_pdf"
                                >
                                    Upload PDF from url
                                </label>
                                <TextInput
                                    id="url_pdf"
                                    required
                                    onChange={(e) =>
                                        setData({ pdfUrl: e.target.value })
                                    }
                                    placeholder="https://example.com/file.pdf"
                                    className="w-full p-2"
                                />
                            </div>
                            <div>
                                <PrimaryButton
                                    type="button"
                                    className="h-10 w-full justify-center"
                                    onClick={handleSubmitUrl}
                                >
                                    {processing ? (
                                        <LoadingDots color="white" />
                                    ) : (
                                        "Submit"
                                    )}
                                </PrimaryButton>
                            </div>
                        </form>
                    </Tab.Panel>
                </Tab.Panels>
            </Tab.Group>
        </div>
    );
}
