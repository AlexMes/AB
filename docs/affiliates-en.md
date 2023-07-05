# Lead registration

-   URL: `https://uleads.app/external/lead`
-   Method: POST
-   Headers: `Content-Type` & `Accept` = `application/json`

## POST data

-   `api_key` - Your unique key
-   `firstname` - required
-   `lastname` - required
-   `middlename`
-   `domain`
-   `phone` - required
-   `ip`
-   `email`
-   `form_type`
-   `utm_source`
-   `utm_content`
-   `utm_campaign`
-   `utm_term`
-   `utm_medium`
-   `clickid` - Sub ID. If sent, must be unique for each lead.

### Success response

Response code 201, body

```json
{
    "message": "Stored",
    "redirect": "https://some.url?token=12345",
    "lead": {
        "id": "09ac276c-4f0b-436c-9f0e-1d17f4e7c9d5",
        "name": "Tommy Lee",
        "phone": "796487799226",
        "valid": true
    }
}
```

### Validation errors

Http code: 422
Body:

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "phone": ["The phone field is required."],
        "api_key": ["The selected api key is invalid."]
    }
}
```
