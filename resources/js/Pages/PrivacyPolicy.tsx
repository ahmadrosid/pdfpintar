import ApplicationLogo from "@/Components/application-logo";
import { Link } from "@inertiajs/react";

export default function PrivacyPolicy() {
    return (
        <div className="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div className="py-4">
                <Link href="/">
                    <ApplicationLogo className="w-20 h-20 text-teal-500" />
                </Link>
            </div>

            <div className="w-full sm:max-w-6xl mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg mb-10">
                <div className="space-y-6">
                    <h1 className="text-4xl font-extrabold text-start py-6">
                        Privacy Policy
                    </h1>
                    <h2 className="font-bold">1. Information We Collect</h2>
                    <p>
                        We may collect personal information such as your name,
                        email address, and other contact information when you
                        voluntarily provide it to us through our website.
                    </p>
                    <h2 className="font-bold">
                        2. How We Use Your Information
                    </h2>
                    <p>
                        We use your personal information to respond to your
                        inquiries, to provide you with information about our
                        products and services, and to improve the content and
                        functionality of our website.
                    </p>
                    <h2 className="font-bold">
                        3. Cookies and Other Tracking Technologies
                    </h2>
                    <p>
                        We use cookies and other tracking technologies to
                        collect information about your use of our website. This
                        information may include your IP address, browser type,
                        operating system, and other information about your
                        device. We use this information to improve the content
                        and functionality of our website and to personalize your
                        experience.
                    </p>
                    <h2 className="font-bold">4. Sharing Your Information</h2>
                    <p>
                        We do not sell or share your personal information with
                        third parties except as required by law or as necessary
                        to fulfill your requests.
                    </p>
                    <h2 className="font-bold">5. Security</h2>
                    <p>
                        We take reasonable measures to protect your personal
                        information from unauthorized access, disclosure, or
                        use.
                    </p>
                    <h2 className="font-bold">6. Children’s Privacy</h2>
                    <p>
                        Our website is not intended for children under the age
                        of 13. We do not knowingly collect personal information
                        from children under the age of 13.
                    </p>
                    <h2 className="font-bold">
                        7. Changes to this Privacy Policy
                    </h2>
                    <p>
                        We may update this Privacy Policy from time to time. We
                        will post the updated policy on our website and will
                        indicate the date of the most recent update.
                    </p>
                    <h2 className="font-bold">8. Contact Us</h2>
                    <p>
                        If you have any questions about this Privacy Policy,
                        please contact us at{" "}
                        <a
                            href="mailto:contact@pdfpintar.com"
                            className="text-teal-700 underline hover:text-teal-500"
                        >
                            contact@pdfpintar.com
                        </a>
                    </p>
                </div>
            </div>
        </div>
    );
}
