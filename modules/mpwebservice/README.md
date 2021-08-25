- Marketplace WebService API Module V6.x.x
- Compatible to Marketplace V5.2.x

### Admin Configuration
- Admin need to Enable PrestaShop's Webservice from 'Advanced Parameters -> Webservice' tab
- Then, Create a WebService Key and provide 'seller' resource permission only


- End URL: /api/seller/[api_name]?auth_key=seller_key&ws_key=admin_key

### Get Sellers
- Method: GET
- EndPoint: /api/seller/sellerinfo/


### Get Seller Products
- Method: GET
- EndPoint: /api/seller/sellerproduct/

- Use filter [From: V5.1.1]
- Get 2 to 5 product: ?limit=2,5
- Get first 3 product: ?limit=3

### GET Seller Profile Images
- EndPoint: /api/seller/mpimages/sellerlogo
- EndPoint: /api/seller/mpimages/sellerbanner
- EndPoint: /api/seller/mpimages/shoplogo
- EndPoint: /api/seller/mpimages/shopbanner

### Upload Seller Profile Images
- EndPoint: /api/seller/mpimages/sellerlogo
- Form Input:
```
<form action="http://domain.com/api/seller/mpimages/sellerlogo?ws_key=seller_key&auth_key=admin_key" method="POST" enctype="multipart/form-data">
<fieldset>
    <legend>Upload Seller Logo</legend>
    <input name="ps_method" value="PUT" type="hidden">
    <input name="image" type="file">
    <input value="Execute" type="submit">
</fieldset>
</form>
```
- Same as above use other endpoint: sellerbanner, shoplogo, shopbanner

### Assign Admin Product to Seller
Admin need to create admin_auth_key from module configuraion page first.
URL: /api/seller/assignproduct/?ws_key=seller_key&admin_auth_key=admin_key
Method: GET
Params: id_seller & id_product [prestashop id product]

### DELETE Seller Profile Images
- Method: DELETE
- /api/seller/mpimages/sellerlogo
- Same you can use other endpoint: sellerbanner, shoplogo, shopbanner

### GET default Seller Profile Image
- EndPoint: /api/seller/mpimages/sellerlogo/default
- Same you can use other endpoint: sellerbanner, shoplogo, shopbanner

### POST/GET/DELETE Seller Images
- PrestaShop URL: http://doc.prestashop.com/display/PS16/Chapter+9+-+Image+management
- For DELETE Product Image
    - Method: DELETE
    - /api/seller/mpimages/products?id_mp_product=18&id_mp_product_image=93
- For POST Product Image
    - Method: POST
    - /api/seller/mpimages/products?id_mp_product=18

### GET Seller product ids which have images
- EndPoint: /api/seller/mpimages/products
- Output:
```
{
    "success": true,
    "productIds": [
        "2",
        "4"
    ]
}
```

### GET Seller product image id list
- EndPoint: /api/seller/mpimages/products/[id_mp_product]
- Output:
```
{
    "success": true,
    "mp_id_product": "2",
    "imageIds": [
        "9",
        "11"
    ]
}
```

### GET Seller product image by image id
- EndPoint: /api/seller/mpimages/products/[id_mp_product]/[id_mp_product_image]
- Where: id_mp_product_image [Image Id return in previous api outpur 'imageIds' key]
- Output: Image

### Upload Seller product image
- EndPoint: /api/seller/mpimages/products/[id_mp_product]
- Form Input:
```
<form action="http://domain.com/api/seller/mpimages/products/2?ws_key=seller_key&auth_key=admin_key" method="POST" enctype="multipart/form-data">
<fieldset>
    <legend>Upload Seller Logo</legend>
    <input name="image" type="file">
    <input value="Execute" type="submit">
</fieldset>
</form>
```
- Output: Image

### DELETE Seller product image
- Method: DELETE
- EndPoint: /api/seller/mpimages/products/[id_mp_product]/[id_mp_product_image]



## Add Seller Product
**Get Product Feature details**
    - Get the node: product_feature -> id and id_feature_value
        - /api/product_feature_values/
**Get Product Combination values**
    - /api/product_option_values/
- Method: POST
- EndPoint: /api/seller/saveproduct
- Input:
```
{
   "id_ps_shop": "1",
   "default_lang": "1",
   "id_category": "4",
   "price": "100",
   "wholesale_price": "250.54",
   "unity": [],
   "unit_price": "0.000000",
   "id_tax_rules_group": "1",
   "quantity": "1000",
   "minimal_quantity": "1",
   "active": "1",
   "condition": "new",
   "available_for_order": "1",
   "show_price": "1",
   "online_only": "0",
   "visibility": "both",
   "width": "1",
   "height": "2",
   "depth": "3",
   "weight": "4",
   "default_category": "3",
   "reference": "dks_demo",
   "ean13": "1234567891234",
   "upc": "876543456787",
   "out_of_stock": "2",
   "available_date": "0000-00-00",
   "ps_id_carrier_reference": [],
   "id_lang": "1",
   "product_category": "6,9",
   "product_name": "Dheeraj Demo Product",
   "short_description": "Printed chiffon knee length dress with tank straps. Deep v-neckline.",
   "description": "Fashion has been creating well-designed collections since 2010. The brand offers feminine designs delivering stylish separates and statement dresses which have since evolved into a full ready-to-wear collection in which every item is a vital part of a woman's wardrobe. The result? Cool, easy, chic looks with youthful elegance and unmistakable signature style. All the beautiful pieces are made in Italy and manufactured with the greatest attention. Now Fashion extends to a range of accessories including shoes, hats, belts and more!",
   "available_now": "In stock",
   "available_later": [],
   "meta_title": "meta demo",
   "meta_description": "meto description text",
   "images": {
      "image": {
         "cover": "1",
         "position": "1",
         "url": live_url_of_image
      }
   },
   "product_features": [
      {
         "id": "5",
         "id_feature_value": "1"
      },
      {
         "id": "6",
         "id_feature_value": "13"
      },
      {
         "id": "7",
         "id_feature_value": "19"
      }
   ],
   "product_combinations": {
      "product_combination": [
        {
         "id_mp_product_attribute": [],
         "product_option_value": [
            "5",
            "19"
         ],
         "reference": "demo_76",
         "ean13": "1234567890123",
         "upc": "784236487234",
         "isbn": "42341234",
         "quantity": "13",
         "price": "100",
         "wholesale_price": "122",
         "unit_price_impact": "12",
         "weight": "3",
         "minimal_quantity": "1",
         "available_date": "2018-04-25",
         "id_images": {
            "id_image": "52"
         }
       }
       {
        ---
       }
       {
        ---
       }
      ]
   }
}
```

### Create Seller Order
- Method: POST
- EndPoint: /api/seller/createorder
- Input:
```
{
   "id_cart": "15",
   "module": "bankwire",
   "id_customer": "2",
   "total_paid": "107",
   "payment": "Bank wire",
   "current_state": "10"
}
```

### Update Seller Order Status
- Method: POST
- EndPoint: /api/seller/updateorderstatus
- Input:
```
{
	"id_order": "11",
	"id_order_state": "2"
}
```
- Output:
```
{
	"success": true,
	"message": "Order status updated successfully."
}
```

### View Seller Order
- Method: GET
- EndPoint: /api/seller/sellerorder
- Output:
```
{
  "success": true,
  "orderList": [
    {
      "id_order_detail": "25",
      "ordered_product_name": "Test",
      "product_price": "8.000000",
      "qty": "1",
      "id_order": "12",
      "buyer_id_customer": "5",
      "total_paid": "12",
      "payment_mode": "Pay by check",
      "reference": "MNURXMWYO",
      "seller_firstname": "Test",
      "seller_lastname": "WEbkul",
      "seller_email": "test@webkul.com",
      "date_add": "2019-01-23 12:45:12",
      "order_status": "Confirmed",
      "id_currency": "1"
    },
    {
      "id_order_detail": "23",
      "ordered_product_name": "Test One",
      "product_price": "8.130081",
      "qty": "1",
      "id_order": "11",
      "buyer_id_customer": "5",
      "total_paid": "10.000000",
      "payment_mode": "Pay by check",
      "reference": "IOEERDFF",
      "seller_firstname": "Test",
      "seller_lastname": "Webkul",
      "seller_email": "test@webkul.com",
      "date_add": "2019-01-23 12:45:12",
      "order_status": "Confirmed",
      "id_currency": "1"
    },
}
```

### View Seller Order Details
- Method: GET
- EndPoint: /api/seller/sellerorder?id_order=11
- Output:
```
Full order details json
```

###  Documentation link
https://webkul.com/blog/prestashop-marketplace-web-service-api-configuration/

### Support Policy link
https://store.webkul.com/support.html/

### Refund Policy link
https://store.webkul.com/refund-policy.html/

### Update Policy
- V6.0.0: Uninstall, delete old version and install
