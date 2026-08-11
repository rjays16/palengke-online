import { alertController } from '@ionic/vue';

export function useModals() {
  const showLoadingModal = async (message = 'Loading...') => {
    const alert = await alertController.create({
      cssClass: 'loading-modal-simple',
      message: message,
      buttons: [],
    });
    await alert.present();
    return alert;
  };

  return { showLoadingModal };
}
