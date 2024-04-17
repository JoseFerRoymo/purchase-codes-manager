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
<style>
  .general{height: calc(100vh - 100px); width:100%; display: flex; flex-direction: column; align-items: center; justify-content: center;}
  .head{font-size: 15px; color: #616161; line-height: 140%;}
  .box-form{background-color: #fff; width: 570px; max-width: 100%; padding: 50px; border-radius: 10px; margin-top: 25px; display: flex; flex-direction: column; align-items: center; gap: 25px;  box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);}
  input.form-verify-invoice, input.form-verify-code{border: 1px solid #05438F; border-radius: 4px; height: 50px; }
  input.form-verify-button{border-radius: 4px; border: none; height: 50px; width: 20%; color: #FFF; background-color: #05438F; }
  input.form-verify-button:hover{cursor: pointer;}
  form.form-verify{width: 100%;}
  input.form-verify-invoice::placeholder, input.form-verify-code::placeholder{color: #929292;}
  .validation-message{display: inline-flex; width: 38%;flex-direction: column;}
  .verify-code{padding: 0px; margin: 0px; font-size: 10px; margin-top: 4px;}

</style>
<div class="general">
  <div>
    <img src="https://plusalute.roymo.info/wp-content/uploads/2024/04/logo-1.svg" alt="Códigos Promocionales">
    
  </div>
  <div class="box-form">
  <span class="head">Introduce el nº de factura de compra y el código promocional aportado por el usuario para validar su participación en el sorteo.</span>
  <form class="form-verify">
    <input class="form-verify-invoice" type="text" placeholder="Número de factura"/>
    <div class="validation-message">
    <input class="form-verify-code" type="text" placeholder="Código promocional"/>
  <p class='verify-code unshow'>El código promocional se ha actualizado correctamente.</p>
      
    </div>
    <input class="form-verify-button" type="submit" value="Verificar"/>
  </form>

</div>
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
      verifyCode.innerText = '*El código promocional ya ha sido verificado';
      verifyCode.classList.remove('unshow');
      verifyCode.classList.add('red');
    }else{
      verifyCode.innerText = '*El código introducido no es válido';
      verifyCode.classList.add('red');
      verifyCode.classList.remove('unshow');
    }

    return data;
  };
  
  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const code = document.querySelector('.form-verify-code').value;
    const invoice = document.querySelector('.form-verify-invoice').value;
    const message = document.querySelector('.verify-code');

    if(!code || !invoice){
      message.innerText = 'Introduce el numero de factura y el código para continuar.';
      message.classList.add('red');
      message.classList.remove('unshow');
      return;
    }

   // const {value: accept} = await Swal.fire({
    // html: `<div class='confirm-container'>
    //  <p class='title'>¿Quieres continuar con los siguientes datos?</p>
    //  <p class='text'>${invoice} - ${code}</p>
    //  </div>`,
    //  icon: 'question',
    //  confirmButtonText: 'Continuar',
    //  confirmButtonColor: '#05438F'
    //});

    //if(accept){
      document.querySelector('.verify-code').classList.remove('unshow');
      updatePurchase(code, invoice);
    //}
  });
</script>

<style>
  .verify-code.unshow{
    display: none;
  }
  .verify-code.red{
    color: #CC0000;
  }
</style>