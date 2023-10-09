import { Link, Head } from "@inertiajs/react";
import { PageProps } from "@/types";
import { Github, Linkedin, Twitter, X } from "lucide-react";
import { Button } from "@/Components/ui/button";
import Modal from "@/Components/modal";
import { useState } from "react";

export default function Welcome({
    auth,
    laravelVersion,
    phpVersion,
}: PageProps<{ laravelVersion: string; phpVersion: string }>) {
    const [demoModalShown, setDemoModalShown] = useState(false);
    return (
        <>
            <Head title="Welcome" />
            <div className="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-square bg-center bg-white dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">
                <div className="top-0 inset-x-0 absolute bg-gradient-to-b from-white to-white/0">
                    <div className="flex justify-between container mx-auto p-4">
                        <div>
                            <p className="font-extrabold tracking-wide text-black">
                                PDFPINTAR
                            </p>
                        </div>
                        <div>
                            {auth.user ? (
                                <Link
                                    href={route("documents.index")}
                                    className="font-medium text-sm text-gray-600 hover:text-gray-900 hover:underline dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-none"
                                >
                                    Documents
                                </Link>
                            ) : (
                                <>
                                    <Link
                                        href={route("login")}
                                        className="font-medium text-sm py-2 px-3 text-gray-600 hover:text-primary dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-none"
                                    >
                                        Log in
                                    </Link>

                                    <Link
                                        href={route("register")}
                                        className="font-medium text-sm ml-4 py-2 px-3 rounded-md text-gray-600 hover:text-primary dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-none"
                                    >
                                        Register
                                    </Link>
                                </>
                            )}
                        </div>
                    </div>
                </div>

                <div className="max-w-7xl mx-auto p-6 lg:p-8">
                    <div className="mt-[70px] max-w-3xl mx-auto pb-8 text-center">
                        <h1 className="text-3xl font-bold text-gray-900 md:text-4xl xl:text-5xl xl:leading-tight">
                            Effortless PDF Comprehension with AI-powered Chatbot
                        </h1>
                        <h2 className="mt-6 leading-snug text-gray-500 xl:mt-5 xl:text-xl">
                            Discover a smarter way to read PDFs, where
                            intelligent conversations unlock deeper
                            comprehension and effectiveness in your reading
                            journey.
                        </h2>
                        <div className="flex gap-4 py-8 justify-center items-center">
                            <a href="/login">
                                <Button>Get started now!</Button>
                            </a>
                            <div>
                                <button
                                    onClick={() => setDemoModalShown(true)}
                                    className="hover:border-primary hover:text-gray-700 font-medium leading-6 text-gray-600 border-b-2"
                                >
                                    Watch Demo
                                </button>
                            </div>
                        </div>
                    </div>
                    <div className="mx-auto max-w-7xl">
                        <img
                            src="/screenshot.png"
                            className="rounded-3xl shadow-2xl ring-[10px] ring-primary/30"
                        />
                    </div>
                </div>
            </div>

            <footer className="bg-white">
                <div className="px-4 py-12 mx-auto max-w-7xl sm:px-6 md:flex md:items-center md:justify-between lg:px-8">
                    <div className="flex justify-center space-x-6 md:order-2">
                        <span className="inline-flex justify-center w-full gap-3 lg:ml-auto md:justify-start md:w-auto">
                            <a
                                href="https://github.com/ahmadrosid"
                                className="w-6 h-6 transition fill-black hover:text-blue-500"
                            >
                                <span className="sr-only">github</span>
                                <Github
                                    className="w-5 h-5 md hydrated"
                                    aria-label="logo github"
                                />
                            </a>
                            <a
                                href="https://twitter.com/_ahmadrosid"
                                className="w-6 h-6 transition fill-black hover:text-blue-500"
                            >
                                <span className="sr-only">twitter</span>
                                <Twitter
                                    className="w-5 h-5 md hydrated"
                                    aria-label="logo twitter"
                                />
                            </a>
                            <a
                                href="https://linkedin.com/in/ahmadrosid"
                                className="w-6 h-6 transition fill-black hover:text-blue-500"
                            >
                                <span className="sr-only">Linkedin</span>
                                <Linkedin
                                    className="w-5 h-5 md hydrated"
                                    role="img"
                                    aria-label="logo linkedin"
                                />
                            </a>
                        </span>
                    </div>
                    <div className="mt-8 md:mt-0 md:order-1">
                        <p className="text-base text-center text-gray-400">
                            <span className="mx-auto mt-2 text-sm text-gray-500">
                                Copyright © 2023
                                <a
                                    href="https://ahmadrosid.com"
                                    className="mx-2 text-blue-500 hover:underline"
                                    rel="noopener noreferrer"
                                >
                                    @ahmadrosid
                                </a>
                            </span>
                        </p>
                    </div>
                </div>
            </footer>

            <Modal
                show={demoModalShown}
                onClose={() => setDemoModalShown(false)}
            >
                <div className="flex justify-between px-4 pt-2">
                    <h3 className="font-bold">Demo</h3>
                    <button onClick={() => setDemoModalShown(false)}>
                        <X size={18} />
                    </button>
                </div>
                <iframe
                    width="650"
                    height="400"
                    className="mx-auto rounded-md my-3"
                    src="https://www.youtube.com/embed/2AR02P5-RQk?si=QfX2QzGGt3_ZZHW-"
                    title="Demo PDFPINTAR"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowFullScreen
                ></iframe>
            </Modal>
        </>
    );
}
