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


$logo =  plugin_dir_url( __FILE__ ) . '../assets/logo-plus-salute.svg';

?>

<div class='page-container'>
  <div class='header-container'>
    <img src='<?= $logo ?>'>
  </div>
  <div class='tools-container'>
    <div class='section-filters'>
      <p>Filtros</p>
      <div class='filters-container'>
        <input id='name' class='filter filter-name' type='text' placeholder='Nombre'/>
        <input id='email' class='filter filter-email' type='text' placeholder='Email'/>
        <input id='phone' class='filter filter-phone' type='text' placeholder='Teléfono'/>
        <input id='location' class='filter filter-place' type='text' placeholder='Localización'/>
        <input id='shop' class='filter filter-shop' type='text' placeholder='Tienda'/>
        <input id='draw_code' class='filter filter-code' type='text' placeholder='Código'/>
        <input id='purchase_invoice' class='filter filter-invoice' type='text' placeholder='Factura'/>
        <label class='label-date'>
          Fecha envio
          <div>
            <input id='date_created' class='filter filter-creation-date' type='date'/>
            <input id='date_created_end' class='subfilter filter-created-end' type='date'/>
          </div>
        </label>
        <label class='label-date'>
          Fecha validacion
          <div>
            <input id='date_redeemed' class='filter filter-creation-date' type='date'/>
            <input id='date_redeemed_end' class='subfilter filter-created-end' type='date'/>
          </div>
        </label>
        <label>
          <input id='verified' class='filter-verified' type='checkbox'/>
          verificado
        </label>
      </div>
    </div>
    <div class='buttons-container'>
      <button class='button-download'>Crear CSV</button>
    </div>
  </div>
  <div class='purchases-container'></div>
</div>



<script>
  const container = document.querySelector('.purchases-container');

  window.addEventListener('load', async() => {
    const request = await get_purchases();
    const purchases = request.req.data;
    let filteredPurchases = purchases;
    
    renderTable(purchases, container);

    document.querySelector('.button-download').addEventListener('click', (e) => {
      e.preventDefault();
      downloadCSV(filteredPurchases);
    });
    
    const filters = document.querySelectorAll('.filter');
    const subFilters = document.querySelectorAll('.subfilter');
    const checkbox = document.querySelector('.filter-verified');
    const dataFilters = {};

    filters.forEach((filter) => {
      filter.addEventListener('input', () => {
        filters.forEach((f) => {
          const id = f.id;
          const value = f.value;
          dataFilters[id] = value;
        });
        filteredPurchases = filterPurchases(dataFilters, purchases);
        renderTable(filteredPurchases, container);
      });
    });

    subFilters.forEach((subfilter) => {
      subfilter.addEventListener('input', () => {
        filters.forEach((f) => {
          const id = f.id;
          const value = f.value;
          dataFilters[id] = value;
        });
        filteredPurchases = filterPurchases(dataFilters, purchases);
        renderTable(filteredPurchases, container);
      })
    });

    checkbox.addEventListener('change', () => {
      filters.forEach((f) => {
        const id = f.id;
        const value = f.value;
        dataFilters[id] = value;
      });
      filteredPurchases = filterPurchases(dataFilters, purchases);
      renderTable(filteredPurchases, container);
    });
  });

  const get_purchases = async() => {
    const response = await fetch('https://plusalute.roymo.info/wp-json/purchase-manager/v1/purchases/');
    const data = await response.json();
    return data;
  }

  const downloadCSV = (purchases) => {
    const rows = [];
    let csvContent = '';

    const headers = Object.keys(purchases[0]);
    rows.push(headers);

    purchases.map((purchase) => {
      rows.push(Object.values(purchase));
    });

    rows.forEach((row) => {
      csvContent += row.join(',') + '\n';
    });

    const blob = new Blob([csvContent], {type: 'test/csv;charset=utf-8,'});
    const objURL = URL.createObjectURL(blob);

    const link = document.createElement('a');
    link.setAttribute('href', objURL);
    link.setAttribute('download', 'compras.csv');
    link.textContent = 'Descargar compras';

    document.querySelector('.buttons-container').appendChild(link);
  }

  const filterPurchases = (filters, purchases) => {
    const check = document.querySelector('.filter-verified');

    const purchaseList =  purchases.map((purchase) => {
      let passesFilter = true;
      for(const key in filters){
        if(!filters[key]) continue;

        if(key === 'date_created' && filters[key]){
          const createdEnd = document.querySelector('.filter-created-end');
          if(createdEnd.value){
            const dateInit = new Date(filters[key]).getTime();
            const dateEnd = new Date(createdEnd.value).getTime();
            const purchaseDate = new Date(purchase[key]).getTime();

            if(!isNaN(purchaseDate) && purchaseDate > dateInit && dateEnd > purchaseDate){
              passesFilter = true;
            }else{
              passesFilter = false;
            }
            continue;
          }
        }

        if(key === 'date_redeemed' && filters[key]){
          const createdEnd = document.querySelector('.filter-created-end');
          if(createdEnd.value){
            const dateInit = new Date(filters[key]).getTime();
            const dateEnd = new Date(createdEnd.value).getTime();
            const purchaseDate = new Date(purchase[key]).getTime();

            if(!isNaN(purchaseDate) && purchaseDate > dateInit && dateEnd > purchaseDate){
              passesFilter = true;
            }else{
              passesFilter = false;
            }
            continue;
          }
        }

        if((new RegExp(filters[key], 'i')).test(purchase[key])){
          passesFilter = true;
        }else{
          passesFilter = false;
        }
      }
      if(!passesFilter) return;
      return purchase;
    });

    if(check.checked){
      return purchaseList.filter((purchase) => {
        if(purchase && purchase.purchase_invoice){
          return purchase;
        }
      });
    }

    return purchaseList.filter((purchase) => purchase);
  }

  const renderTable = (purchases, container) => {
    let html = `<table>
    <tr>
    <th>Nombre</th>
    <th>Email</th>
    <th>Teléfono</th>
    <th>Localización</th>
    <th>Tienda</th>
    <th>Código</th>
    <th>Factura</th>
    <th>Fecha de creación</th>
    <th>Fecha de validación</th>
    </tr>`;

    purchases.forEach((purchase, index) => {
      html += `<tr>
      <td>${purchase.name}</td>
      <td>${purchase.email}</td>
      <td>${purchase.phone}</td>
      <td>${purchase.location}</td>
      <td>${purchase.shop}</td>
      <td>${purchase.draw_code}</td>
      <td>${purchase.purchase_invoice ? purchase.purchase_invoice : 'Sin validar'}</td>
      <td>${purchase.date_created}</td>
      <td>${purchase.date_redeemed ? purchase.date_redeemed : 'Sin validar'}</td>
      </tr>`;

      if(index === purchases.length){
        html += '</table>';
      }
    });

    container.innerHTML = html;
  }
</script>

<style>
  .page-container{
    display: flex;
    flex-direction: column;
  }
  .header-container{
    display: flex;
    width: 100%;
    justify-content: center;
    align-items: center;
    padding: 30px 0px;
  }
  .header-container img{
    max-width: 300px;
  }
  .purchases-container{
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
  }
  .purchases-container table{
    width: 98%;
    background-color: #FFFFFF;
    border-radius: 8px;
    border: none;
    border-collapse: collapse;
    overflow: hidden;
    margin: 30px 0px;
  }
  .purchases-container table tr td,
  .purchases-container table tr th{
    padding: 10px 5px;
    font-size: 15px;
  }
  .purchases-container table tr th{
    color: #FFFFFF;
    text-align: start;
    font-weight: 500;
  }
  .purchases-container table tr:nth-child(odd){
    background-color: #f8f8f8;
  }
  .purchases-container table tr:nth-child(1){
    background-color: #05438F;
  }
  .buttons-container{
    width: 98%;
    display: flex;
    justify-content: start;
    align-self: center;
    align-items: center;
    gap: 30px;
    flex-wrap: wrap;
  }
  .buttons-container a{
    font-size: 16px;
    color: #05438F;
  }
  .button-download{
    padding: 15px 25px;
    border: none;
    border-radius: 8px;
    background-color: #05438F;
    color: #FFFFFF;
    font-size: 15px;
  }
  .section-filters{
    display: flex;
    flex-direction: column;
    align-self: center;
    flex-wrap: wrap;
  }
  .section-filters p{
    font-size: 15px;
  }
  .section-filters .filters-container{
    display: flex;
    flex-direction: row;
    gap: 20px;
    flex-wrap: wrap;
    align-items: end;
  }
  .section-filters .filters-container .filter{
    margin: 0px;
    height: fit-content;
  }
  .tools-container{
    display: flex;
  }
  .tools-container .section-filters{
    flex: 3;
  }
  .tools-container .buttons-container{
    flex: 1;
  }
  .label-date{
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
  .label-date div{
    display: flex;
    gap: 15px;
  }
  .tools-container{
    width: 98%;
    align-self: center;
  }
</style>