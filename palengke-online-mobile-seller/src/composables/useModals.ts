import { alertController } from '@ionic/vue';
import { useSuccessModal } from './useSuccessModal';

interface ConfirmDialogOptions {
  header: string;
  message: string;
  confirmText?: string;
  cancelText?: string;
  destructive?: boolean;
  onConfirm: () => void | Promise<void>;
}

export function useModals() {
  const { showSuccessModal } = useSuccessModal();

  const showLoadingModal = async (message = 'Loading...') => {
    const alert = await alertController.create({
      cssClass: 'loading-modal-simple',
      message,
      buttons: [],
    });
    await alert.present();
    return alert;
  };

  const showConfirmDialog = async (options: ConfirmDialogOptions) => {
    const {
      header,
      message,
      confirmText = 'Confirm',
      cancelText = 'Cancel',
      destructive = false,
      onConfirm,
    } = options;

    const alert = await alertController.create({
      header,
      message,
      buttons: [
        { text: cancelText, role: 'cancel' },
        {
          text: confirmText,
          role: destructive ? 'destructive' : undefined,
          handler: async () => {
            await onConfirm();
          },
        },
      ],
    });

    await alert.present();
  };

  return { showSuccessModal, showLoadingModal, showConfirmDialog };
}
