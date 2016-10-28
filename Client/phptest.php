<?php
/**
 * Created by PhpStorm.
 * User: linyulin
 * Date: 16/10/28
 * Time: 下午4:05
 */
include 'JWT.php';
$jwt = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VybmFtZSI6ImxpbjA1MCIsInVzZXJfaWQiOiIxIiwibmlja2NoZW4iOiJNZW1vcnkiLCJhdmF0YXIiOiJodHRwczpcL1wvc3MxLmJhaWR1LmNvbVwvNk9OWHNqaXAwUUlaOHR5aG5xXC9pdFwvdT0zMDc0NTY1MDEwLDMwNjgwNjU5ODcmZm09ODAmdz0xNzkmaD0xMTkmaW1nLkpQRUciLCJleHAiOjE0Nzc2NDUzMDcsImlhdCI6MTQ3NzY0MTcwN30.OGUyZDkyZTM2MmIyYmM4MGFlZGExY2FkZmUxMTJiYTg4ODQxMmRkMzZmYzQwNDk4MmFlOTExNjZjYzJlZDMzYw";
print_r(JWT::decode($jwt, 'memory'));