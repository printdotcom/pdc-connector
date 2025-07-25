# Print.com Mock API with WireMock

This directory contains WireMock configuration for mocking the Print.com API during development and testing.

## 🎯 Overview

The mock API provides realistic responses based on the actual Print.com API documentation at [developer.print.com](https://developer.print.com/).

## 🚀 Quick Start

```bash
# Start the mock API
bin/run-mock-api start

# Check status
bin/run-mock-api status

# Stop the mock API
bin/run-mock-api stop
```

## 📁 Structure

```
test/wiremock/
├── mappings/           # WireMock request/response mappings
│   ├── products.get.json    # Lists products
├── __files/            # Static response files
│   └── products.json    # Products data
└── README.md          # This file
```

## 🔗 Available Endpoints

### Products
- **GET** `/products` - List all products
- **POST** `/orders` - Places an order

## 🔑 Authentication

The mock API requires an API key in the `X-API-Key` header for protected endpoints.

**Valid Test API Keys:**
- `test_key_12345`
- `dev_key_67890`

## 🔧 Customization

### Adding New Endpoints

1. Create a new mapping file in `mappings/`:
```json
{
  "request": {
    "method": "GET",
    "urlPathEqualTo": "/new-endpoint"
  },
  "response": {
    "status": 200,
    "jsonBody": {
      "message": "New endpoint response"
    }
  }
}
```

2. Restart the mock API:
```bash
bin/run-mock-api restart
```

### Using Static Files

For large responses, create files in `__files/` and reference them:
```json
{
  "response": {
    "status": 200,
    "bodyFileName": "large-response.json"
  }
}
```

## 🐛 Troubleshooting

### Mock API Won't Start
- Check if port 8001 is available
- Ensure Docker is running
- Verify the `pdc` network exists (created by `run-wordpress`)

### Authentication Errors
- Ensure you're including the `X-API-Key` header
- Use one of the valid test API keys listed above

## 📖 References

- [WireMock Documentation](http://wiremock.org/docs/)
- [Print.com API Documentation](https://developer.print.com/)
