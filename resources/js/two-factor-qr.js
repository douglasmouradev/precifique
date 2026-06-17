import QRCode from 'qrcode';

export function initTwoFactorQr() {
    const canvas = document.getElementById('two-factor-qr-canvas');
    if (!canvas) {
        return;
    }

    const uri = canvas.dataset.qrUri;
    if (!uri) {
        return;
    }

    QRCode.toCanvas(canvas, uri, { width: 180, margin: 1 }).catch(() => {});
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTwoFactorQr);
} else {
    initTwoFactorQr();
}
