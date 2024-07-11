# Order Tracking API

This repo is a microservice I'd written a few years ago that integrates with courier service such as Aramex, Bex, and Fastnfurious.

### Usage

Make a `POST` request to the root endpoint as `JSON` data.

```
curl --location 'http://localhost:8000/' \
--header 'Content-Type: application/json' \
--data '{ 
    "tracking_company": "Bex|Amarex|fast furious", 
    "tracking_numbers": ["fulfillment.tracking_number"]
}'
```