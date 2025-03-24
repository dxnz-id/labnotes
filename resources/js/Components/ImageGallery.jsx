import React, { useEffect, useRef } from "react";
import anime from "animejs";

const ImageGallery = () => {
    const sectionRef = useRef(null);
    const isScrolling = useRef(false);

    useEffect(() => {
        const section = sectionRef.current;

        // Event listener untuk mendeteksi scroll manual
        const handleScroll = () => {
            isScrolling.current = true;
            setTimeout(() => {
                isScrolling.current = false;
            }, 1000); // Durasi deteksi scroll manual
        };

        section.addEventListener("scroll", handleScroll);

        const scrollAnimation = () => {
            if (!isScrolling.current) {
                anime({
                    targets: section,
                    scrollLeft: section.scrollLeft + 1, // Scroll horizontal
                    duration: 1000, // Kecepatan animasi
                    easing: "linear",
                    complete: () => {
                        if (
                            section.scrollLeft >=
                            section.scrollWidth - section.clientWidth
                        ) {
                            section.scrollLeft = 0; // Reset ke awal
                        }
                        scrollAnimation(); // Loop animasi
                    },
                });
            } else {
                setTimeout(scrollAnimation, 1000); // Tunggu sebelum restart animasi
            }
        };

        scrollAnimation();

        return () => {
            section.removeEventListener("scroll", handleScroll);
        };
    }, []);

    // Fungsi untuk mengacak array
    const shuffleArray = (array) => {
        return array
            .map((item) => ({ item, sort: Math.random() }))
            .sort((a, b) => a.sort - b.sort)
            .map(({ item }) => item);
    };

    // Data gambar sampel dari API placeholder dengan aspek rasio berbeda
    const images = shuffleArray([
        "https://picsum.photos/249/201",
        "https://picsum.photos/412/352",
        "https://picsum.photos/278/219",
        "https://picsum.photos/299/323",
        "https://picsum.photos/209/208",
        "https://picsum.photos/242/484",
        "https://picsum.photos/307/399",
        "https://picsum.photos/214/267",
        "https://picsum.photos/481/469",
        "https://picsum.photos/390/315",
        "https://picsum.photos/409/280",
        "https://picsum.photos/449/411",
        "https://picsum.photos/211/334",
        "https://picsum.photos/376/499",
        "https://picsum.photos/374/315",
        "https://picsum.photos/306/347",
        "https://picsum.photos/328/257",
        "https://picsum.photos/293/416",
        "https://picsum.photos/280/443",
        "https://picsum.photos/298/466",
        "https://picsum.photos/229/357",
        "https://picsum.photos/450/290",
        "https://picsum.photos/267/234",
        "https://picsum.photos/306/375",
        "https://picsum.photos/347/405",
        "https://picsum.photos/305/259",
        "https://picsum.photos/283/389",
        "https://picsum.photos/442/348",
        "https://picsum.photos/461/279",
        "https://picsum.photos/201/297",
        "https://picsum.photos/223/422",
        "https://picsum.photos/414/463",
        "https://picsum.photos/253/287",
        "https://picsum.photos/257/488",
        "https://picsum.photos/492/396",
        "https://picsum.photos/468/485",
        "https://picsum.photos/360/270",
        "https://picsum.photos/392/429",
        "https://picsum.photos/296/460",
        "https://picsum.photos/450/319",
        "https://picsum.photos/288/351",
        "https://picsum.photos/351/340",
        "https://picsum.photos/395/302",
        "https://picsum.photos/256/487",
        "https://picsum.photos/234/414",
        "https://picsum.photos/200/267",
        "https://picsum.photos/289/499",
        "https://picsum.photos/486/282",
        "https://picsum.photos/483/481",
        "https://picsum.photos/495/316",
        "https://picsum.photos/466/388",
        "https://picsum.photos/225/263",
        "https://picsum.photos/233/358",
        "https://picsum.photos/431/431",
        "https://picsum.photos/270/420",
        "https://picsum.photos/344/280",
        "https://picsum.photos/402/490",
        "https://picsum.photos/210/349",
        "https://picsum.photos/242/401",
        "https://picsum.photos/369/442",
        "https://picsum.photos/322/231",
        "https://picsum.photos/422/428",
        "https://picsum.photos/273/339",
        "https://picsum.photos/488/410",
        "https://picsum.photos/492/261",
        "https://picsum.photos/372/407",
        "https://picsum.photos/375/358",
        "https://picsum.photos/435/339",
        "https://picsum.photos/370/393",
        "https://picsum.photos/337/246",
        "https://picsum.photos/326/343",
        "https://picsum.photos/478/271",
        "https://picsum.photos/492/423",
        "https://picsum.photos/404/246",
        "https://picsum.photos/202/255",
        "https://picsum.photos/396/235",
        "https://picsum.photos/267/462",
        "https://picsum.photos/380/496",
        "https://picsum.photos/219/472",
        "https://picsum.photos/222/377",
        "https://picsum.photos/255/276",
        "https://picsum.photos/472/428",
        "https://picsum.photos/333/360",
        "https://picsum.photos/442/449",
        "https://picsum.photos/482/338",
        "https://picsum.photos/214/404",
        "https://picsum.photos/420/214",
        "https://picsum.photos/212/420",
        "https://picsum.photos/230/424",
        "https://picsum.photos/351/223",
        "https://picsum.photos/244/242",
        "https://picsum.photos/413/449",
        "https://picsum.photos/274/252",
        "https://picsum.photos/288/247",
        "https://picsum.photos/369/256",
        "https://picsum.photos/496/479",
        "https://picsum.photos/366/372",
        "https://picsum.photos/338/366",
        "https://picsum.photos/432/346",
        "https://picsum.photos/342/203",
    ]);

    return (
        <div
            ref={sectionRef}
            style={{ scrollBehavior: "smooth", columnCount: 4 }}
            className="m-3 rounded-md"
        >
            {images.map((src, index) => (
                <div key={index}>
                    <img
                        src={src}
                        alt=""
                        className="w-full shadow h-full rounded-md object-cover mb-3 border-2 transition border-black/20 dark:border-white/20"
                    />
                </div>
            ))}
        </div>
    );
};

export default ImageGallery;
