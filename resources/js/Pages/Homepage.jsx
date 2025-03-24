import Cursor from '@/Components/Cursor';
import Footer from '@/Components/Footer';
import Navbar from '@/Components/Navbar';
import { Head } from '@inertiajs/react';
import { useEffect } from 'react';
import { BsArrowRight } from 'react-icons/bs';

export default function Homepage() {
    useEffect(() => {
        const textArray = [
            'Center of Excellence.',
            'Pusat Keunggulan.',
            "SMK Assa'idiyah Kudus.",
        ];
        let currentIndex = 0;
        let currentText = '';
        let isDeleting = false;
        let isBlinking = false; // State to control blinking
        let cursorBlinkInterval;

        const type = () => {
            const fullText = textArray[currentIndex];
            currentText = isDeleting
                ? fullText.substring(0, currentText.length - 1)
                : fullText.substring(0, currentText.length + 1);

            document.getElementById('type').innerText = currentText;

            // Stop blinking while typing
            if (cursorBlinkInterval) {
                clearInterval(cursorBlinkInterval);
                document.getElementById('typeCursor').style.opacity = '1'; // Ensure cursor is visible
            }

            if (!isDeleting && currentText === fullText) {
                // Start blinking when typing is complete
                isBlinking = true;
                startBlinking();
                setTimeout(() => {
                    isDeleting = true;
                    isBlinking = false;
                    type();
                }, 5000);
            } else if (isDeleting && currentText === '') {
                // Start blinking when deletion is complete
                isBlinking = true;
                startBlinking();
                setTimeout(() => {
                    isDeleting = false;
                    currentIndex = (currentIndex + 1) % textArray.length;
                    isBlinking = false;
                    type();
                }, 500);
            } else {
                const speed = isDeleting ? 50 : 100;
                setTimeout(type, speed);
            }
        };

        const startBlinking = () => {
            if (!isBlinking) return; // Only start blinking if allowed
            cursorBlinkInterval = setInterval(() => {
                const cursor = document.getElementById('typeCursor');
                if (cursor) {
                    cursor.style.opacity =
                        cursor.style.opacity === '0' ? '1' : '0';
                }
            }, 500);
        };

        type();

        // Cleanup interval on component unmount
        return () => {
            clearInterval(cursorBlinkInterval);
        };
    }, []);

    return (
        <>
            <Head title="Portfolio" />
            <Cursor />
            <div className="flex flex-col items-center justify-center text-gray-900 transition dark:bg-black dark:text-white">
                <Navbar />

                <section
                    className="container flex h-screen items-center justify-center text-center"
                    id="section-1"
                >
                    <div>
                        <p className="">
                            <span id="type" className="text-primary"></span>
                            <span id="typeCursor" className="transition">
                                |
                            </span>
                        </p>
                        <h1 className="text-4xl font-bold md:text-5xl">
                            <span className="text-primary">CoE Laboratory</span>
                        </h1>
                        <a
                            href="/admin"
                            className="group mt-4 flex items-center justify-center space-x-2 opacity-75 transition hover:opacity-90"
                        >
                            <span className="transition">Dashboard</span>
                            <BsArrowRight className="text-3xl transition-transform group-hover:translate-x-2" />
                        </a>
                    </div>
                </section>
                <Footer />
            </div>
        </>
    );
}
