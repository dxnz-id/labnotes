import AnimatedCursor from 'react-animated-cursor';

const Cursor = () => {
    return (
        <AnimatedCursor
            innerSize={10}
            outerSize={25}
            color="178, 178, 178"
            outerAlpha={0.2}
            innerScale={1}
            outerScale={2}
            innerStyle={{
                mixBlendMode: 'difference',
            }}
            clickables={[
                'a',
                'button',
                'input',
                'nav',
                "[role='button']",
                '.custom-clickable',
            ]}
        />
    );
};

export default Cursor;
