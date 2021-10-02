# Rock star shop Challenge

A RESTful Laravel development challenge for managing a small coffee shop

## Introduction

In this challenge, you are going to develop a small Laravel web application which manages
Rock star coffee shop orders via REST APIs.

In Rock star, the manager can define variety of products via admin panel. Customers are able
to order and customize their coffee with several options. Order can have a status: waiting,
preparation, ready, delivered. Manager can change orders status. After each status change on
order, you should notify the customer via email.

Here is sample catalog of products offered by Rock star:

- Production > Customization option
- Latte > Milk: skim, semi, whole
- Cappuccino > Size: small, medium, large
- Espresso > Shots: single, double, triple
- Tea
- Hot chocolate > Size: small, medium, large
- Cookie > Kind: chocolate chip, ginger
- All > Consume location: take away, in shop

For the sake of simplicity, consider each product has a constant price no matter which option
is selected.

The following REST APIs should be implemented and any customer should be able to
consume the API using a secure way:

- View Menu (list of products)
- Order at coffee shop with options
- View his order (product list, pricing & order status)
- Change a waiting order
- Cancel a waiting orders

API response format is up to you.

This is a typical coffee shop problem which you're probably familiar with. All defined
requirements are just for testing your programming skills, they might not be meaningful in a
real world example.

## Tests
- Write unit tests for your code with good coverage
