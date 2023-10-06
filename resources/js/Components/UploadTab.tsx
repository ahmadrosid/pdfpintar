import UploadFile from "./UploadFile";
import { toast } from "react-hot-toast";
import { useForm } from "@inertiajs/react";
import LoadingDots from "./LoadingDots";
import { Input } from "@/Components/ui/input";
import { Label } from "@/Components/ui/label";
import { Separator } from "./ui/separator";

export default function UploadTab() {
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
            <form className="pb-4">
                <div className="space-y-2">
                    <Label htmlFor="url_pdf">Upload from url</Label>
                    <Input
                        id="url_pdf"
                        required
                        onChange={(e) => setData({ pdfUrl: e.target.value })}
                        placeholder="https://example.com/file.pdf"
                        className="w-full p-2"
                    />
                </div>
            </form>
            <Separator className="my-4" />
            <UploadFile />
        </div>
    );
}
