# IMS suppliers and PO creation

Source-based illustrative examples. Supplier endpoints are public. PO transfer creation, item addition and create-order requests require a bearer token. Set base_url, supplier_id, lookupcode, token, transfer_request_id and item_hq_id. PO workflow: create transfer, add items, create order using either status or status. Validation returns only the first error.

Import the collection into Postman. base_url is the server root without /api or a trailing slash. Responses are illustrative source-based examples, not live captures. Use a token user with a valid store and cashier. Choose one create-order version for a given transfer.

## List suppliers

GET {{base_url}}/api/suppliers

All suppliers ordered by SupplierName; no pagination or filters.

### 200 — Suppliers found

```json
{
  "status": 200,
  "message": "Suppliers retrieved successfully",
  "data": [
    {
      "id": 1,
      "hq_id": 101,
      "code": "SUP001",
      "supplier_name": "Example Supplier",
      "contact_name": "Contact",
      "phone_number": "01000000000",
      "email_address": "supplier@example.com",
      "country": "Egypt",
      "state": null,
      "city": "Cairo",
      "last_updated": "2026-10-01 10:00:00"
    }
  ]
}
```

### 200 — Empty list

```json
{
  "status": 200,
  "message": "Suppliers retrieved successfully",
  "data": []
}
```

## Supplier details and items

GET {{base_url}}/api/suppliers/{{supplier_id}}

supplier_id is Supplier.ID. Includes all linked items ordered by ItemID. items[].id is Item.ID; orphaned item relations yield null lookupcode/description. No separate /suppliers/{supplier}/items route.

### 200 — With items

```json
{
  "status": 200,
  "message": "Supplier retrieved successfully",
  "data": {
    "id": 1,
    "hq_id": 101,
    "code": "SUP001",
    "supplier_name": "Example Supplier",
    "contact_name": "Contact",
    "phone_number": "01000000000",
    "email_address": "supplier@example.com",
    "items": [
      {
        "id": 20,
        "lookupcode": "ITEM001",
        "description": "Example Item"
      }
    ]
  }
}
```

### 200 — Without items

```json
{
  "status": 200,
  "message": "Supplier retrieved successfully",
  "data": {
    "id": 1,
    "hq_id": 101,
    "code": "SUP001",
    "supplier_name": "Example Supplier",
    "contact_name": "Contact",
    "phone_number": "01000000000",
    "email_address": "supplier@example.com",
    "items": []
  }
}
```

### 404 — Supplier missing

```json
{
  "status": 404,
  "message": "Resource not found.",
  "data": null,
  "exception": "No query results for model [App\\Models\\Supplier] 999999"
}
```

## Search supplier item

GET {{base_url}}/api/suppliers/{{supplier_id}}/items/search?lookupcode={{lookupcode}}

Required lookupcode query string; exact ItemLookupCode match subject to DB collation, no partial/name/alias search. Trims whitespace, converts blank to null. Item must exist and belong to supplier. data.id is Item.HQID, unlike the supplier items list. Supplier binding precedes validation. For array validation failure send lookupcode[]=ITEM001.

### 200 — Linked item found

```json
{
  "status": 200,
  "message": "Supplier item search completed successfully",
  "data": {
    "id": 200,
    "lookupcode": "ITEM001",
    "description": "Example Item"
  }
}
```

### 404 — Supplier missing

```json
{
  "status": 404,
  "message": "Resource not found.",
  "data": null,
  "exception": "No query results for model [App\\Models\\Supplier] 999999"
}
```

### 404 — Item missing

```json
{
  "status": 404,
  "message": "Item not found",
  "data": null
}
```

### 404 — Existing item not linked

```json
{
  "status": 404,
  "message": "Item is not related to this supplier",
  "data": null
}
```

### 422 — Missing lookupcode

```json
{
  "status": 422,
  "message": "Item lookup code is required.",
  "data": null
}
```

## Create transfer as PO

POST {{base_url}}/api/transfer-requests

Creates an open transfer request, not the final purchase order. type must be exactly PO. supplier_id is required integer Supplier.ID. other_store_id is optional for PO; omit it. store_id comes from the token user. Optional title (string max 255); omit to use the server-generated title. Optional delivery_date must be a date today or later; omit when unused, not null. Response type is [] for PO in the current enum. items is omitted on creation. Use the returned id as transfer_request_id, add items, then create order. Current default PO title uses the generic Transfer Out template; supply title for a clear PO name.

Request body:

```json
{
  "type": "PO",
  "supplier_id": "{{supplier_id}}",
  "title": "Supplier purchase order"
}
```

### 201 — Created

```json
{
  "status": 201,
  "message": "Transfer request created successfully",
  "data": {
    "id": 25,
    "title": "Supplier purchase order",
    "from_store_id": 1,
    "to_store_id": null,
    "to_store_name": null,
    "supplier_id": 1,
    "status": "open",
    "type": [],
    "delivery_date": null,
    "purchase_order_id": null,
    "created_at": "2026-10-01",
    "updated_at": "2026-10-01"
  }
}
```

### 422 — Supplier missing

```json
{
  "status": 422,
  "message": "You must specify a supplier.",
  "data": null
}
```

### 422 — Supplier not found

```json
{
  "status": 422,
  "message": "The selected supplier does not exist.",
  "data": null
}
```

### 422 — Invalid supplier ID

```json
{
  "status": 422,
  "message": "Invalid supplier ID format.",
  "data": null
}
```

### 422 — Type missing

```json
{
  "status": 422,
  "message": "Transfer type is required.",
  "data": null
}
```

### 422 — Invalid type (current source message)

```json
{
  "status": 422,
  "message": "Transfer type must be either TransferOut or TransferIn.",
  "data": null
}
```

### 422 — Title too long

```json
{
  "status": 422,
  "message": "The title cannot exceed 255 characters.",
  "data": null
}
```

### 422 — Invalid delivery date

```json
{
  "status": 422,
  "message": "The delivery date field must be a valid date.",
  "data": null
}
```

### 422 — Past delivery date

```json
{
  "status": 422,
  "message": "The delivery date field must be a date after or equal to today.",
  "data": null
}
```

### 401 — Missing or invalid token

```json
{
  "success": false,
  "message": "Unauthenticated."
}
```

### 401 — Expired token

```json
{
  "success": false,
  "message": "Token expired."
}
```

## Add item to PO transfer

POST {{base_url}}/api/transfer-requests/{{transfer_request_id}}/items

Prerequisite for creating an order. id is Item.HQID, matching supplier-item search data.id; it is not supplier-details items[].id. quantity is required numeric; current rules do not enforce a positive minimum. notes is optional nullable string max 1000. Existing items are updated, otherwise added. No supplier-item membership validation is implemented here.

Request body:

```json
{
  "id": "{{item_hq_id}}",
  "quantity": 2,
  "notes": "Optional note"
}
```

### 200 — Item added

```json
{
  "status": 200,
  "message": "Item added successfully",
  "data": {
    "transfer_request_id": 25,
    "item_id": 200,
    "LookupCode": "ITEM001",
    "Description": "Example Item",
    "quantity": 2,
    "notes": "Optional note",
    "created_at": "2026-10-01 10:00:00",
    "updated_at": "2026-10-01 10:00:00"
  }
}
```

### 200 — Item updated

```json
{
  "status": 200,
  "message": "Item updated successfully",
  "data": {
    "transfer_request_id": 25,
    "item_id": 200,
    "LookupCode": "ITEM001",
    "Description": "Example Item",
    "quantity": 2,
    "notes": "Optional note",
    "created_at": "2026-10-01 10:00:00",
    "updated_at": "2026-10-01 10:00:00"
  }
}
```

### 404 — Transfer not found

```json
{
  "status": 404,
  "message": "Resource not found.",
  "data": null,
  "exception": "No query results for model [App\\Models\\TransferRequest] 999999"
}
```

### 422 — Item missing

```json
{
  "status": 422,
  "message": "Validation failed",
  "data": "Item ID is required."
}
```

### 422 — Item not found

```json
{
  "status": 422,
  "message": "Validation failed",
  "data": "The selected item does not exist."
}
```

### 422 — Quantity missing

```json
{
  "status": 422,
  "message": "Validation failed",
  "data": "Quantity is required."
}
```

### 422 — Quantity invalid

```json
{
  "status": 422,
  "message": "Validation failed",
  "data": "The quantity field must be a number."
}
```

### 401 — Missing or invalid token

```json
{
  "success": false,
  "message": "Unauthenticated."
}
```

### 401 — Expired token

```json
{
  "success": false,
  "message": "Token expired."
}
```

## Create order

POST {{base_url}}/api/transfer-requests/{{transfer_request_id}}/status

Creates the actual order using the stored transfer type, supplier and items. No request body needed. Requires existing items and a cashier associated with the token user. For PO, downstream Order uses SupplierID and OtherStoreID=0; successful order closes the transfer and saves the downstream id as purchase_order_id (can be null if downstream omits id). No closed-status rejection is implemented. Downstream rejection status/message are forwarded with data:null; there is no fixed local list of downstream errors. V1 success text mentions a sync job even for PO; PO itself does not dispatch that job.

### 200 — PO created and transfer closed

```json
{
  "status": 200,
  "message": "Transfer request status updated successfully. PO sync job dispatched.",
  "data": {
    "id": 25,
    "title": "Supplier purchase order",
    "from_store_id": 1,
    "to_store_id": null,
    "to_store_name": null,
    "supplier_id": 1,
    "status": "closed",
    "type": [],
    "delivery_date": null,
    "purchase_order_id": 500,
    "created_at": "2026-10-01",
    "updated_at": "2026-10-01",
    "items": [
      {
        "transfer_request_id": 25,
        "item_id": 200,
        "LookupCode": "ITEM001",
        "Description": "Example Item",
        "quantity": 2,
        "notes": "Optional note",
        "created_at": "2026-10-01 10:00:00",
        "updated_at": "2026-10-01 10:00:00"
      }
    ]
  }
}
```

### 404 — Transfer not found

```json
{
  "status": 404,
  "message": "Resource not found.",
  "data": null,
  "exception": "No query results for model [App\\Models\\TransferRequest] 999999"
}
```

### 406 — No items

```json
{
  "status": 406,
  "message": "No items were found",
  "data": []
}
```

### 404 — Cashier not found

```json
{
  "status": 404,
  "message": "Cashier not found",
  "data": null
}
```

### 422 — PO supplier missing

```json
{
  "status": 422,
  "message": "A supplier is required for this order type.",
  "data": null
}
```

### 409 — Already processing

```json
{
  "status": 409,
  "message": "This transfer request is already being processed. Please wait."
}
```

### 401 — Missing or invalid token

```json
{
  "success": false,
  "message": "Unauthenticated."
}
```

### 401 — Expired token

```json
{
  "success": false,
  "message": "Token expired."
}
```

## PO conditions

POST {{base_url}}/api/purchase-order/{{purchase_order_id}}/conditions

PO only (POType 0 = PO, 1 = HQ PO). Records or updates the PO delivery conditions and supplier invoice number via /api/po-conditions. A separate step from commit: call it before or after a partial receipt, but not after the PO is closed. supplier_invoice_number is required; vehicle_type, vehicle_tempOut, vehicle_tempIN, delivery_permit_number, notes, seal_number, Driver_Name, Vehicle_Number and Goods_Type are optional and only sent when provided. Other types are rejected (TransferIN/TransferOut send their conditions in the commit). Shares the commit lock for the order. This request targets the IMS wrapper: purchase_order_id comes from the URL and is added to the upstream payload. Do not send StoreID. Known upstream issue observed in testing: an invoice-only insert returns HTTP 500 because vehicle_type is non-nullable in the backend database. Supplying full conditions is a workaround until backend optional-field handling is fixed. Response type is a JSON integer: 0=PO, 1=HQ PO, 2=TransferIN, 4=HQ TransferIN; 3/5 are TransferOut or ReturnToSupplier, distinguished by other-store versus supplier IDs.

Request body:

```json
{
    "supplier_invoice_number": "INV-2026-4471",
    "vehicle_type": "Truck",
    "vehicle_tempOut": -5,
    "vehicle_tempIN": -4.2,
    "delivery_permit_number": "DP-1001",
    "notes": "Cold chain",
    "seal_number": "SEAL-001",
    "Driver_Name": "Ahmed",
    "Vehicle_Number": "ABC-123",
    "Goods_Type": 1
}
```

### 500 — Invoice-only insert: observed backend limitation

Request:

```json
{
    "supplier_invoice_number": "INV-2026-4471"
}
```

Response:

```json
{
    "status": 500,
    "message": "Cannot insert the value NULL into column 'vehicle_type', table 'Zamalek.dbo.IMS_PurchaseOrder_Conditions'; column does not allow nulls. INSERT fails.\r\nThe statement has been terminated.",
    "data": null
}
```

### 200 — Full conditions

```json
{
    "status": 200,
    "message": "Purchase Order Conditions Saved Successfully",
    "data": {
        "id": 78912,
        "title": "PO from Acme Foods to Main Store",
        "type": 0,
        "status": 1,
        "from_store_id": 7,
        "from_store_name": "Main Store",
        "to_store_id": 0,
        "to_store_name": null,
        "created_at": "2026-10-05",
        "po_number": "PO-78912",
        "supplier_id": 1,
        "supplier_name": "Acme Foods",
        "items": [],
        "condition": {
            "vehicle_type": "Truck",
            "purchase_order_id": 78912,
            "vehicle_tempOut": -5,
            "vehicle_tempIN": -4.2,
            "delivery_permit_number": "DP-1001",
            "status": null,
            "notes": "Cold chain",
            "seal_number": "SEAL-001",
            "driver_name": "Ahmed",
            "vehicle_number": "ABC-123",
            "goods_type": 1,
            "receiver_name": null,
            "created_at": "2026-10-05"
        }
    }
}
```

### 422 — Invoice number missing

Request:

```json
[]
```

Response:

```json
{
    "status": 422,
    "message": "Supplier invoice number is required.",
    "data": null
}
```

### 422 — Not a PO

Request:

```json
{
    "supplier_invoice_number": "INV-2026-4471"
}
```

Response:

```json
{
    "status": 422,
    "message": "Conditions are not supported for this purchase order type",
    "data": null
}
```

### 409 — PO already closed

Request:

```json
{
    "supplier_invoice_number": "INV-2026-4471"
}
```

Response:

```json
{
    "status": 409,
    "message": "Purchase order is already committed",
    "data": null
}
```

### 409 — Being processed

Request:

```json
{
    "supplier_invoice_number": "INV-2026-4471"
}
```

Response:

```json
{
    "status": 409,
    "message": "This order is already being processed. Please wait.",
    "data": null
}
```

### 400 — Rejected by order service

Request:

```json
{
    "supplier_invoice_number": "INV-2026-4471"
}
```

Response:

```json
{
    "status": 400,
    "message": "PO is not Open or Partial",
    "data": null
}
```

### 401 — Missing or invalid token

Request:

```json
{
    "supplier_invoice_number": "INV-2026-4471"
}
```

Response:

```json
{
    "message": "Unauthenticated."
}
```

## Commit order

POST {{base_url}}/api/purchase-order/{{purchase_order_id}}/commit

Commits the order to the order service. The type comes from the order's POType plus its SupplierID / OtherStoreID, so the client never sends a transaction type:

- 0 / 1 (PO): `isClosed` picks the mode. 1 closes the order (status 2); 0 is a partial receipt (status 1) and can be repeated.
- 2 / 4 (TransferIN): same `isClosed` behaviour as PO, plus the vehicle temperature.
- 3 / 5 with OtherStoreID and no SupplierID (TransferOut): one commit, always closes.
- 3 / 5 with SupplierID and no OtherStoreID (ReturnToSupplier): one commit, always closes, no body needed.

A closed order (status 2) cannot be committed again. Only one commit per order runs at a time. After a successful commit the PDF, emails and print run in the background.

### Request bodies

PO:

```json
{
    "isClosed": 1
}
```

TransferIN (`vehicle_TempIN` is required; the other fields are only used on a partial receipt):

```json
{
    "isClosed": 0,
    "vehicle_TempIN": 4,
    "vehicleType": "Truck",
    "vehicle_TempOut": 3,
    "deliveryPermitNumber": "DP-1001",
    "notes": "Cold chain",
    "seal_number": "SEAL-001",
    "driver_Name": "Ahmed",
    "vehicle_Number": "ABC-123",
    "goods_Type": 1
}
```

TransferOut (`VehicleType` and `seal_number` are required):

```json
{
    "VehicleType": "Truck",
    "seal_number": "SEAL-001",
    "Vehicle_tempOut": 4,
    "DeliveryPermitNumber": "DP-1001",
    "Notes": "Cold chain",
    "goods_type": 1,
    "driver_name": "Ahmed",
    "vehicle_number": "ABC-123"
}
```

ReturnToSupplier:

```json
{}
```

### 200 — Committed

```json
{
    "status": 200,
    "message": "Purchase Order Committed Successfully",
    "data": {
        "id": 201481,
        "title": "RTS test flow",
        "type": 3,
        "status": "2",
        "from_store_id": "7",
        "from_store_name": "Zamalek",
        "to_store_id": "0",
        "to_store_name": null,
        "created_at": "2026-10-06",
        "po_number": "IMS201481",
        "supplier_id": "200",
        "supplier_name": null,
        "items": [
            {
                "id": "2267001",
                "item_id": "3880",
                "purchase_order_id": "201481",
                "LookupCode": "6223001300472",
                "Description": "5 Stars Whole Meal Flour 1Kg",
                "price": "47.3000",
                "quantity_on_hand": 3,
                "quantity_ordered": "0.0",
                "quantity_received": "0.0",
                "quantity_received_to_date ": "0.0",
                "infos": []
            }
        ],
        "condition": null
    }
}
```

### 422 — Validation failed

PO / TransferIN without `isClosed`, `isClosed` not 0 or 1, TransferIN without the vehicle temperature, TransferOut without `VehicleType` or `seal_number`. The message is the first failed rule.

```json
{
    "status": 422,
    "message": "isClosed is required.",
    "data": null
}
```

### 422 — Order has both or neither of SupplierID / OtherStoreID (POType 3 / 5)

```json
{
    "status": 422,
    "message": "Order must have either OtherStoreID or SupplierID",
    "data": null
}
```

### 409 — Already committed

```json
{
    "status": 409,
    "message": "Purchase order is already committed",
    "data": null
}
```

### 409 — Being processed

```json
{
    "status": 409,
    "message": "This order is already being processed. Please wait.",
    "data": null
}
```

### 400 — Invalid purchase order type

```json
{
    "status": 400,
    "message": "Invalid purchase order type",
    "data": null
}
```

### 404 — Cashier not found

```json
{
    "status": 404,
    "message": "Cashier not found",
    "data": null
}
```

### 4xx — Rejected by order service

The order service's status and message are passed through.

```json
{
    "status": 400,
    "message": "PO is not Open or Partial",
    "data": null
}
```

### 502 — Order service unreachable

```json
{
    "status": 502,
    "message": "Could not reach the commit service: ...",
    "data": null
}
```

### 401 — Missing or invalid token

```json
{
    "success": false,
    "message": "Unauthenticated."
}
```

## List items

GET {{base_url}}/api/items?page=1

Public endpoint. Optional page (default 1), fixed 2000 items per page. Optional last_updated filters LastUpdated strictly greater than supplied value; e.g. append &last_updated=2026-10-01%2000:00:00. Only Inactive=0 and HQID<>0 items are returned. No lookup/name search supported by this endpoint. data.page contains items and data.pagination contains navigation. Aliases is an array of alias strings; suppliers_ids is an array of Supplier.ID values. HQID is used for adding transfer items. No explicit last_updated validation is implemented.

### 200 — Items found

```json
{
  "status": 200,
  "message": "Items retrieved successfully",
  "data": {
    "page": [
      {
        "ID": 20,
        "ItemLookupCode": "ITEM001",
        "Description": "Example Item",
        "HQID": 200,
        "Aliases": [
          "ALT001"
        ],
        "suppliers_ids": [
          1
        ],
        "Price": 25,
        "Quantity": 10,
        "LastUpdated": "2026-10-01 10:00:00",
        "DateCreated": "2026-09-01 10:00:00"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 2000,
      "total": 1,
      "last_page": 1,
      "next_page_url": null,
      "prev_page_url": null,
      "current_page_url": "http://localhost/IMS/api/items?page=1"
    }
  }
}
```

### 200 — No items match

```json
{
  "status": 200,
  "message": "Items retrieved successfully",
  "data": {
    "page": [],
    "pagination": {
      "current_page": 1,
      "per_page": 2000,
      "total": 0,
      "last_page": 1,
      "next_page_url": null,
      "prev_page_url": null,
      "current_page_url": "http://localhost/IMS/api/items?page=1"
    }
  }
}
```


## Lookup item

GET {{base_url}}/api/items/{{lookupcode}}

Requires bearer token. Matches ItemLookupCode LIKE lookup%, returning the first matching item without an explicit sort. This is a prefix lookup, not an exact search; aliases are not searched. SQL LIKE wildcards in the input are not escaped. No active/HQID filter is applied. Success data.id is Item.HQID. Current implementation does not handle no match: ShowItemResource accesses a null model, causing a 500 instead of an Item not found 404. Saved missing-item example assumes APP_DEBUG=false; debug=true exposes the exception message and trace.

### 200 — Item found

```json
{
  "status": 200,
  "message": "Item Retrieved Successfully",
  "data": {
    "id": 200,
    "lookupcode": "ITEM001",
    "description": "Example Item"
  }
}
```

### 401 — Missing or invalid token

```json
{
  "success": false,
  "message": "Unauthenticated."
}
```

### 401 — Expired token

```json
{
  "success": false,
  "message": "Token expired."
}
```

### 500 — No matching item (current behavior, debug off)

```json
{
  "status": 500,
  "message": "An unexpected error occurred. Please try again later.",
  "data": null,
  "trace": null
}
```

