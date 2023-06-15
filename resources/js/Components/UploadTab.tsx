import { cn } from "@/lib/utils";
import { Tab } from "@headlessui/react";
import UploadFile from "./UploadFile";

export default function UploadTab() {
    const data = ["Upload File", "From URL"];
    return (
        <div className="py-4">
            <Tab.Group>
                <Tab.List className="flex space-x-1 rounded-xl bg-teal-950/20 p-1">
                    {data.map((item) => (
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
                            "rounded-xl bg-white p-3",
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
                        Content 2
                    </Tab.Panel>
                </Tab.Panels>
            </Tab.Group>
        </div>
    );
}
