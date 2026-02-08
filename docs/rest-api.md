# Products API

## Setup

You need 3 things:

```
CMS_BASE_URL = https://example.com
USERNAME = admin
PASSWORD = xxxx xxxx xxxx xxxx xxxx xxxx
```

API Base: `https://example.com/wp-json/wp-products/v1`

---

## Endpoints

### Products

```
GET    /products           - Get all products
GET    /products/{id}      - Get single product
POST   /products           - Create product
PUT    /products/{id}      - Update product
DELETE /products/{id}      - Delete product
```

### Categories

```
GET    /categories         - Get all categories
GET    /categories/{id}    - Get single category
POST   /categories         - Create category
PUT    /categories/{id}    - Update category
DELETE /categories/{id}    - Delete category
```

### Products by Category

```
GET    /categories/{id}/products  - Get products in category
```

---

## Query Parameters

**Products:**
- `per_page` - Items per page (default: 10)
- `page` - Page number (default: 1)
- `search` - Search products
- `category` - Filter by category ID
- `orderby` - Sort by: date, title, id
- `order` - ASC or DESC

**Categories:**
- `per_page` - Items per page (default: 10)
- `page` - Page number (default: 1)
- `search` - Search categories
- `hide_empty` - true/false
- `parent` - Parent category ID

---

## cURL Examples

**Get products:**
```bash
curl https://example.com/wp-json/wp-products/v1/products \
  -u "admin:xxxx xxxx xxxx xxxx xxxx xxxx"
```

**Get single product:**
```bash
curl https://example.com/wp-json/wp-products/v1/products/123 \
  -u "admin:xxxx xxxx xxxx xxxx xxxx xxxx"
```

**Get products with pagination:**
```bash
curl https://example.com/wp-json/wp-products/v1/products?per_page=20&page=1 \
  -u "admin:xxxx xxxx xxxx xxxx xxxx xxxx"
```

**Search products:**
```bash
curl https://example.com/wp-json/wp-products/v1/products?search=laptop \
  -u "admin:xxxx xxxx xxxx xxxx xxxx xxxx"
```

**Create product:**
```bash
curl -X POST https://example.com/wp-json/wp-products/v1/products \
  -u "admin:xxxx xxxx xxxx xxxx xxxx xxxx" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "New Product",
    "price": "99.99",
    "status": "publish"
  }'
```

**Update product:**
```bash
curl -X PUT https://example.com/wp-json/wp-products/v1/products/123 \
  -u "admin:xxxx xxxx xxxx xxxx xxxx xxxx" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Updated Product",
    "price": "89.99"
  }'
```

**Delete product:**
```bash
curl -X DELETE https://example.com/wp-json/wp-products/v1/products/123 \
  -u "admin:xxxx xxxx xxxx xxxx xxxx xxxx"
```

**Get categories:**
```bash
curl https://example.com/wp-json/wp-products/v1/categories \
  -u "admin:xxxx xxxx xxxx xxxx xxxx xxxx"
```

**Get products by category:**
```bash
curl https://example.com/wp-json/wp-products/v1/categories/5/products \
  -u "admin:xxxx xxxx xxxx xxxx xxxx xxxx"
```

---

## Product Fields

**Required:**
- `title` - Product name

**Optional:**
- `description` - Full description
- `excerpt` - Short description
- `status` - publish, draft, pending
- `price` - Regular price
- `sale_price` - Sale price
- `sku` - Product SKU
- `stock_quantity` - Stock amount
- `stock_status` - instock, outofstock
- `categories` - Array of category IDs

---

## Category Fields

**Required:**
- `name` - Category name

**Optional:**
- `slug` - URL slug
- `description` - Category description
- `parent` - Parent category ID (0 for top-level)

---

## Response Headers

- `X-WP-Total` - Total number of items
- `X-WP-TotalPages` - Total number of pages