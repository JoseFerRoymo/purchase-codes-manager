<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://https://roymo.es/
 * @since      1.0.0
 *
 * @package    Purchase_Codes_Manager
 * @subpackage Purchase_Codes_Manager/admin/partials
 */

 $nonce = 'dH9CeGem3LvAxkDz3N';
?>

<div>
  <form class="form-verify">
    <input class="form-verify-invoice" type="text" placeholder="Número de factura"/>
    <input class="form-verify-code" type="text" placeholder="Código promocional"/>
    <input class="form-verify-button" type="submit" value="Verificar"/>
  </form>

  <p class='verify-code unshow'>El código promocional se ha actualizado correctamente.</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  const form = document.querySelector(".form-verify");
  const nonce = "<?= $nonce ?>";

  const updatePurchase = async (code, invoice) => {
    const res = await fetch(`https://plusalute.roymo.info/wp-json/purchase-manager/v1/purchase/${code}`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        purchase_invoice: invoice,
        draw_code: code,
        nonce: nonce
      })
    });
    const data = await res.json();
    const verifyCode = document.querySelector('.verify-code');

    if(data.req.status === 201){
      verifyCode.classList.remove('unshow');
    }else if(data.req.status === 203){
      verifyCode.innerText = 'El codigo ya ha sido verificado';
      verifyCode.classList.remove('unshow');
    }

    document.querySelector('.verify-code').classList.remove('unshow');
    return data;
  };
  
  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const code = document.querySelector('.form-verify-code').value;
    const invoice = document.querySelector('.form-verify-invoice').value;

    const {value: accept} = await Swal.fire({
      html: `<div class='confirm-container'>
      <p class='title'>¿Quieres continuar con los siguientes datos?</p>
      <p class='text'>${invoice} - ${code}</p>
      </div>`,
      icon: 'question',
      confirmButtonText: 'Continuar',
      confirmButtonColor: '#05438F'
    });

    if(accept){
      updatePurchase(code, invoice);
    }
  });
</script>

<style>
  .verify-code.unshow{
    display: none;
  }
</style>