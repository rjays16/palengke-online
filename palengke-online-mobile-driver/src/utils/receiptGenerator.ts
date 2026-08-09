export interface ReceiptRow {
  label: string;
  value: string;
}

export interface ReceiptData {
  transactionId: string;
  title: string;
  amount: number;
  date: string;
  rows: ReceiptRow[];
  account: string;
  footer?: string;
}

function drawRoundedRect(
  ctx: CanvasRenderingContext2D,
  x: number,
  y: number,
  w: number,
  h: number,
  r: number,
) {
  ctx.beginPath();
  ctx.moveTo(x + r, y);
  ctx.lineTo(x + w - r, y);
  ctx.quadraticCurveTo(x + w, y, x + w, y + r);
  ctx.lineTo(x + w, y + h - r);
  ctx.quadraticCurveTo(x + w, y + h, x + w - r, y + h);
  ctx.lineTo(x + r, y + h);
  ctx.quadraticCurveTo(x, y + h, x, y + h - r);
  ctx.lineTo(x, y + r);
  ctx.quadraticCurveTo(x, y, x + r, y);
  ctx.closePath();
}

function drawDashedLine(ctx: CanvasRenderingContext2D, x1: number, y: number, x2: number) {
  ctx.setLineDash([4, 4]);
  ctx.beginPath();
  ctx.moveTo(x1, y);
  ctx.lineTo(x2, y);
  ctx.stroke();
  ctx.setLineDash([]);
}

export async function generateReceipt(data: ReceiptData): Promise<Blob> {
  const W = 400;
  const H = 620;
  const canvas = document.createElement('canvas');
  canvas.width = W * 2;
  canvas.height = H * 2;
  const ctx = canvas.getContext('2d')!;
  ctx.scale(2, 2);

  // Background
  ctx.fillStyle = '#f5f5f5';
  ctx.fillRect(0, 0, W, H);

  // Card background
  const cardX = 24;
  const cardY = 16;
  const cardW = W - 48;
  const cardH = H - 32;
  ctx.fillStyle = '#ffffff';
  drawRoundedRect(ctx, cardX, cardY, cardW, cardH, 16);
  ctx.fill();
  ctx.strokeStyle = '#e0e0e0';
  ctx.lineWidth = 1;
  ctx.stroke();

  // Green header
  const headerH = 80;
  ctx.save();
  ctx.beginPath();
  ctx.moveTo(cardX + 16, cardY);
  ctx.lineTo(cardX + cardW - 16, cardY);
  ctx.quadraticCurveTo(cardX + cardW, cardY, cardX + cardW, cardY + 16);
  ctx.lineTo(cardX + cardW, cardY + headerH);
  ctx.lineTo(cardX, cardY + headerH);
  ctx.lineTo(cardX, cardY + 16);
  ctx.quadraticCurveTo(cardX, cardY, cardX + 16, cardY);
  ctx.closePath();
  ctx.fillStyle = '#4caf50';
  ctx.fill();
  ctx.restore();

  // Header text
  ctx.fillStyle = '#ffffff';
  ctx.font = 'bold 20px Inter, sans-serif';
  ctx.textAlign = 'center';
  ctx.fillText('Palengke Online', W / 2, cardY + 35);
  ctx.font = '13px Inter, sans-serif';
  ctx.fillStyle = 'rgba(255,255,255,0.85)';
  ctx.fillText(data.title, W / 2, cardY + 55);

  // Checkmark circle
  const checkY = cardY + headerH + 30;
  ctx.beginPath();
  ctx.arc(W / 2, checkY, 22, 0, Math.PI * 2);
  ctx.fillStyle = '#e8f5e9';
  ctx.fill();
  ctx.strokeStyle = '#4caf50';
  ctx.lineWidth = 2;
  ctx.stroke();

  // Checkmark
  ctx.strokeStyle = '#4caf50';
  ctx.lineWidth = 3;
  ctx.lineCap = 'round';
  ctx.lineJoin = 'round';
  ctx.beginPath();
  ctx.moveTo(W / 2 - 8, checkY);
  ctx.lineTo(W / 2 - 2, checkY + 7);
  ctx.lineTo(W / 2 + 9, checkY - 6);
  ctx.stroke();

  // "Payment Successful" text
  ctx.fillStyle = '#1a1a1a';
  ctx.font = 'bold 16px Inter, sans-serif';
  ctx.textAlign = 'center';
  ctx.fillText('Payment Successful', W / 2, checkY + 42);

  // Amount
  ctx.fillStyle = '#059669';
  ctx.font = 'bold 28px Inter, sans-serif';
  ctx.fillText(`₱${data.amount.toLocaleString()}`, W / 2, checkY + 74);

  // Dashed separator
  const sepY = checkY + 92;
  drawDashedLine(ctx, cardX + 20, sepY, cardX + cardW - 20);

  // Receipt details
  const detailsY = sepY + 24;
  const labelX = cardX + 24;
  const valueX = cardX + cardW - 24;
  ctx.textAlign = 'left';
  ctx.font = '12px Inter, sans-serif';

  const rows: [string, string][] = [
    ['Transaction ID', data.transactionId],
    ['Date', data.date],
    ...data.rows.map((r): [string, string] => [r.label, r.value]),
    ['Account', data.account],
  ];

  let rowY = detailsY;
  rows.forEach(([label, value], i) => {
    ctx.fillStyle = '#9ca3af';
    ctx.fillText(label, labelX, rowY);
    ctx.fillStyle = '#1a1a1a';
    ctx.font = 'bold 12px Inter, sans-serif';
    ctx.textAlign = 'right';
    ctx.fillText(value, valueX, rowY);
    ctx.font = '12px Inter, sans-serif';
    ctx.textAlign = 'left';
    rowY += 28;

    if (i < rows.length - 1) {
      ctx.strokeStyle = '#f0f0f0';
      ctx.lineWidth = 1;
      ctx.beginPath();
      ctx.moveTo(labelX, rowY - 10);
      ctx.lineTo(valueX, rowY - 10);
      ctx.stroke();
    }
  });

  // Dashed separator
  drawDashedLine(ctx, cardX + 20, rowY + 4, cardX + cardW - 20);

  // Footer
  const footerY = rowY + 30;
  ctx.textAlign = 'center';
  ctx.fillStyle = '#6b7280';
  ctx.font = '11px Inter, sans-serif';
  ctx.fillText(data.footer || 'Thank you for your purchase!', W / 2, footerY);
  ctx.fillText('Palengke Online — Your Digital Palengke', W / 2, footerY + 18);

  // Small green accent bar at bottom of card
  ctx.fillStyle = '#4caf50';
  drawRoundedRect(ctx, cardX, cardY + cardH - 8, cardW, 8, 0);
  ctx.fill();

  return new Promise((resolve) => {
    canvas.toBlob((blob) => resolve(blob!), 'image/png', 1.0);
  });
}

export async function downloadReceipt(data: ReceiptData) {
  const blob = await generateReceipt(data);
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `palengke-receipt-${data.transactionId}.png`;
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
}

export async function shareReceipt(data: ReceiptData) {
  const blob = await generateReceipt(data);
  const file = new File([blob], `palengke-receipt-${data.transactionId}.png`, {
    type: 'image/png',
  });

  const shareText = `Palengke Online ${data.title}\n\n${data.rows
    .map((r) => `${r.label}: ${r.value}`)
    .join('\n')}\nAmount: ₱${data.amount.toLocaleString()}\nDate: ${data.date}\nTransaction ID: ${data.transactionId}`;

  if (navigator.share && navigator.canShare?.({ files: [file] })) {
    try {
      await navigator.share({
        title: 'Palengke Online Receipt',
        text: shareText,
        files: [file],
      });
      return true;
    } catch {
      return false;
    }
  }

  return false;
}

export function receiptShareText(data: ReceiptData): string {
  return `Palengke Online ${data.title}\n\n${data.rows
    .map((r) => `${r.label}: ${r.value}`)
    .join('\n')}\nAmount: ₱${data.amount.toLocaleString()}\nDate: ${data.date}\nTransaction ID: ${data.transactionId}`;
}

export async function shareReceiptWithFallback(data: ReceiptData): Promise<void> {
  const shared = await shareReceipt(data);
  if (shared) return;

  const text = receiptShareText(data);
  const { actionSheetController, toastController } = await import('@ionic/vue');

  const actionSheet = await actionSheetController.create({
    header: 'Share Receipt',
    buttons: [
      {
        text: 'Share via WhatsApp',
        handler: () => {
          window.open(`https://wa.me/?text=${encodeURIComponent(text)}`, '_blank');
        },
      },
      {
        text: 'Share via Messenger',
        handler: () => {
          window.open(
            `https://www.facebook.com/dialog/send?link=${encodeURIComponent(window.location.href)}&app_id=&redirect_uri=${encodeURIComponent(window.location.href)}`,
            '_blank',
          );
        },
      },
      {
        text: 'Share via Viber',
        handler: () => {
          window.open(`viber://forward?text=${encodeURIComponent(text)}`, '_blank');
        },
      },
      {
        text: 'Share via Facebook',
        handler: () => {
          window.open(
            `https://www.facebook.com/sharer/sharer.php?quote=${encodeURIComponent(text)}`,
            '_blank',
          );
        },
      },
      {
        text: 'Copy Receipt Text',
        handler: async () => {
          try {
            await navigator.clipboard.writeText(text);
          } catch {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
          }
          const toast = await toastController.create({
            message: 'Receipt text copied!',
            duration: 2000,
            color: 'success',
            position: 'top',
          });
          await toast.present();
        },
      },
      { text: 'Cancel', role: 'cancel' },
    ],
  });
  await actionSheet.present();
}
