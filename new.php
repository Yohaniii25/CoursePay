curl --location 'https://test-bankofceylon.mtf.gateway.mastercard.com/api/rest/version/100/merchant/TEST700182200500/session' \
--header 'Content-Type: application/json' \
--header 'Authorization: Basic bWVyY2hhbnQuVEVTVDcwMDE4MjIwMDUwMDpiMWUzZTE1NjU3MWNlNGFhZTRmNzMzZTVmMWY1MGYyMw==' \
--data ' {
    "apiOperation": "INITIATE_CHECKOUT",
    "checkoutMode": "WEBSITE",
    "interaction":{
        "operation" :"PURCHASE",
         "merchant": { 
            "name": "Gem and Jewellery",
            "url":  "https://sltdigital.site/gem/"
        },
        "returnUrl": "https://sltdigital.site/gem/complete.php"
    },
    "order": {
        "currency":"LKR",
        "amount": "250.00",
        "id" : "1234",
        "description": "Goods and Services"
    }
 } 

'