import { useEffect, useState } from 'react';
import {
    HiOutlineDesktopComputer,
    HiOutlineMoon,
    HiOutlineSun,
} from 'react-icons/hi'; // Import outline icons

const Navbar = () => {
    const [mode, setMode] = useState(
        () => localStorage.getItem('theme') || 'system',
    );

    useEffect(() => {
        const applyTheme = () => {
            if (mode === 'dark') {
                document.documentElement.classList.add('dark');
            } else if (mode === 'light') {
                document.documentElement.classList.remove('dark');
            } else {
                // 'system' mode: Follow the user's system preference
                const prefersDark = window.matchMedia(
                    '(prefers-color-scheme: dark)',
                ).matches;
                if (prefersDark) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            }
        };

        applyTheme();
        localStorage.setItem('theme', mode);
    }, [mode]);

    const toggleMode = () => {
        const nextMode =
            mode === 'light' ? 'dark' : mode === 'dark' ? 'system' : 'light';
        setMode(nextMode);
    };

    const getIcon = () => {
        if (mode === 'light') return <HiOutlineSun className="text-xl" />;
        if (mode === 'dark') return <HiOutlineMoon className="text-xl" />;
        return <HiOutlineDesktopComputer className="text-xl" />;
    };

    return (
        <div className="fixed top-0 z-50 flex w-full justify-center">
            <nav className="flex w-full items-center justify-center bg-white transition dark:bg-black">
                <div className="container mx-0 flex items-center justify-between px-10 py-4">
                    {/* App Name */}
                    <div className="flex">
                        <a
                            href="/"
                            className="px-4 font-semibold transition duration-200 hover:text-purple-500 dark:text-gray-300 dark:hover:text-purple-500"
                        >
                            {import.meta.env.VITE_APP_NAME || 'Lorem Ipsum'}
                        </a>
                    </div>

                    {/* Single Toggle Button */}
                    <div className="flex items-center hover:text-purple-500">
                        <button
                            onClick={toggleMode}
                            className="rounded-full p-2"
                            title={`Current mode: ${mode}`}
                        >
                            {getIcon()}
                        </button>
                    </div>
                </div>
            </nav>
        </div>
    );
};

export default Navbar;
