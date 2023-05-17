import { Link, Head } from "@inertiajs/react";
import { PageProps } from "@/types";

export default function Welcome({
    auth,
    laravelVersion,
    phpVersion,
}: PageProps<{ laravelVersion: string; phpVersion: string }>) {
    return (
        <>
            <Head title="Welcome" />
            <div className="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">
                <div className="sm:fixed sm:top-0 sm:right-0 p-6 text-right">
                    {auth.user ? (
                        <Link
                            href={route("documents.index")}
                            className="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-none"
                        >
                            Documents
                        </Link>
                    ) : (
                        <>
                            <Link
                                href={route("login")}
                                className="py-2 px-3 text-gray-600 hover:text-green-500 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-none"
                            >
                                Log in
                            </Link>

                            <Link
                                href={route("register")}
                                className="ml-4 py-2 px-3 rounded-md text-gray-600 hover:text-green-500 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-none"
                            >
                                Register
                            </Link>
                        </>
                    )}
                </div>

                <div className="max-w-7xl mx-auto p-6 lg:p-8">
                    <h1 className="text-7xl font-extrabold tracking-tight">
                        Welcome to PDF pintar.
                    </h1>
                    <div>
                        <img src="/demo.png" />
                    </div>
                </div>
            </div>
        </>
    );
}
