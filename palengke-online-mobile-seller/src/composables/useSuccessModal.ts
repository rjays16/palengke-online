import { alertController } from '@ionic/vue';

interface SuccessModalOptions {
  message: string;
  autoDismissMs?: number;
  onDismiss?: () => void;
  awaitDismiss?: boolean;
}

export function useSuccessModal() {
  const showSuccessModal = async (options: SuccessModalOptions | string) => {
    const opts = typeof options === 'string' ? { message: options } : options;
    const { message, autoDismissMs = 2000, onDismiss, awaitDismiss = false } = opts;

    const alert = await alertController.create({
      cssClass: 'success-modal',
      header: ' ',
      message,
      buttons: ['OK'],
    });

    await alert.present();

    setTimeout(() => {
      const alertElement = document.querySelector('.success-modal .alert-message');
      if (alertElement) {
        const textContent = alertElement.textContent;
        alertElement.innerHTML = '';

        const wrapper = document.createElement('div');
        wrapper.style.display = 'flex';
        wrapper.style.flexDirection = 'column';
        wrapper.style.alignItems = 'center';
        wrapper.style.textAlign = 'center';

        const img = document.createElement('img');
        img.src = '/assets/images/checkmark_success.png';
        img.style.width = '80px';
        img.style.height = '80px';
        img.style.margin = '0 auto 16px auto';
        img.style.display = 'block';

        const textDiv = document.createElement('div');
        textDiv.textContent = textContent;
        textDiv.style.fontSize = '18px';
        textDiv.style.fontWeight = '600';
        textDiv.style.color = '#1a1a1a';
        textDiv.style.textAlign = 'center';

        wrapper.appendChild(img);
        wrapper.appendChild(textDiv);
        alertElement.appendChild(wrapper);
      }
    }, 50);

    if (awaitDismiss) {
      await alert.onDidDismiss();
      onDismiss?.();
    } else if (autoDismissMs > 0) {
      setTimeout(() => {
        alert.dismiss();
        onDismiss?.();
      }, autoDismissMs);
    }
  };

  return { showSuccessModal };
}
