import * as React from 'react';

import {createRoot} from 'react-dom/client';

export default function App() {
    return <>Viva la vite!</>;
}

const root = document.querySelector("#root");
if (root) {
    createRoot(root).render(<App/>);
}
