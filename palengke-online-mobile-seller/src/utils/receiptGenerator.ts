export interface ReceiptData {
  transactionId: string;
  planName: string;
  amount: number;
  paymentMethod: string;
  date: string;
  validUntil: string;
  sellerEmail: string;
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
  ctx.fillText('Payment Receipt', W / 2, cardY + 55);

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
    ['Plan', data.planName],
    ['Payment Method', data.paymentMethod],
    ['Valid Until', data.validUntil],
    ['Account', data.sellerEmail],
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
  ctx.fillText('Thank you for your purchase!', W / 2, footerY);
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

  const shareText = `Palengke Online Payment Receipt\n\nPlan: ${data.planName}\nAmount: ₱${data.amount.toLocaleString()}\nPayment: ${data.paymentMethod}\nDate: ${data.date}\nValid Until: ${data.validUntil}\nTransaction ID: ${data.transactionId}`;

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
