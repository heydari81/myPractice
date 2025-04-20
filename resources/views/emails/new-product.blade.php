@component('mail::message')
    # New Product Added

    A new product has been added to the Product Management system.

    **Product Details:**
    - **Name**: {{ $product->name }}
    - **Description**: {{ $product->description }}
    - **Price**: ${{ number_format($product->price, 2) }}
    - **Stock**: {{ $product->stock }}

    @component('mail::button', ['url' => url('/api/products/' . $product->id)])
        View Product
    @endcomponent

    Thank you for managing our products!

    Best regards,
    Product Management Team
@endcomponent
