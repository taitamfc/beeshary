# Apple Pay + Stripe Recurring Payments Prestashop Module

#### About

Stripe is the best payment gateways available in the market. Accept onsite payments, and allows to save card information for the next purchases. A beautiful, optimized, cross-device payment form, with support for single click payments.
Stripe provides the facility to handle the recurring payments and this module provide you the ability to do a lot more for the recurring payments.

#### How Recurring payments/ Subscriptions will work?

STEP 1. Create any number of recurring Plans in your Stripe account
STEP 2. Synchronize Plans from Stripe account using "Syncronize Stripe Data" tab of this module configuration page. It'll import all the recurring plans from stripe.
STEP 3. On the product edit page you can use the "Stripe Recurring Payments" tab to add any stripe recurring plan to that product. You can also set if you want create a direct charge for that product amount including adding a subscription as well.
STEP 4. Now on any subscription product purchase, module will create a subscription for the customer and also direct charge him if you've set any product to charge the amount.

Thats all to set a recurring payment/ Subscription for a customer.

#### Module Confuguration

--------Technical Checks----------
  You can see all the required server configuration and required module settings in order to work this module perfactly.  

--------Stripe Connexion----------
  You can set many the stripe settings including the Secret & Publishable keys for both modes of LIVE & TEST.
  
--------Stripe Checkout----------
  You can set here if you want to use Stripe hosted checkout form or a custom embedded form. For hosted checkout you can set Pop-up logo, title, description & locale.
   
--------Order Statuses----------
  You can set Order Statuses for different stripe events.

--------Subscription Products----------
  You can see all the Subscription Products with the Edit button for each, which takes you to product edit page to change the stripe settings.
  
--------Syncronize Stripe Data----------
  You can do the Syncronization for the Stripe Data using different options there.
  
--------Test Credit Card Numbers----------
  All the Test Credit Card Numbers are listed here for all the transactions in the TEST mode.
  
--------Stripe Webhooks----------
  There is the Webhook URL that you can set in your Stripe's account. It'll work on 3 events as follows:
  1. Chargeback information. (Stripe Event type: charge.dispute.created)
  2. First subscription payment confirmation. (Stripe Event type: invoice.payment_succeeded)
  3. Customer subscription update. (Stripe Event type: customer.subscription.updated)

And You're done.