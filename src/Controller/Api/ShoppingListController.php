<?php

namespace App\Controller\Api;

use App\Entity\Item;
use App\Entity\ShoppingList;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ShoppingListController
{
    // ---------------------------
    // POST /lists
    // Create a new list (optionally with items)
    // Body: { "name": "Supermarkt", "items": [ { "name": "Banane", "quantity": 4 } ] }
    // ---------------------------
    #[Route('/lists', name: 'api_lists_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Invalid JSON body'], 400);
        }

        $name = trim((string)($data['name'] ?? ''));
        if ($name === '') {
            return new JsonResponse(['error' => 'Field "name" is required'], 400);
        }

        $list = new ShoppingList();
        $list->setName($name);

        $em->persist($list);

        $items = $data['items'] ?? [];
        if ($items !== null && !is_array($items)) {
            return new JsonResponse(['error' => '"items" must be an array'], 400);
        }

        if (is_array($items)) {
            foreach ($items as $i) {
                if (!is_array($i)) {
                    continue;
                }

                $itemName = trim((string)($i['name'] ?? ''));
                if ($itemName === '') {
                    continue;
                }

                $quantity = (int)($i['quantity'] ?? 1);
                if ($quantity < 1) {
                    $quantity = 1;
                }

                $item = new Item();
                $item->setName($itemName);
                $item->setQuantity($quantity);
                $item->setShoppingList($list);

                $em->persist($item);
            }
        }

        $em->flush();

        return new JsonResponse($this->serializeList($list), 201);
    }

    // ---------------------------
    // POST /lists/{id}/item
    // Add one item to a list
    // Body: { "name": "Brot", "quantity": 1 }
    // ---------------------------
    #[Route('/lists/{id}/item', name: 'api_lists_add_item', methods: ['POST'])]
    public function addItem(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $list = $em->getRepository(ShoppingList::class)->find($id);
        if (!$list) {
            return new JsonResponse(['error' => 'List not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Invalid JSON body'], 400);
        }

        $name = trim((string)($data['name'] ?? ''));
        if ($name === '') {
            return new JsonResponse(['error' => 'Field "name" is required'], 400);
        }

        $quantity = (int)($data['quantity'] ?? 1);
        if ($quantity < 1) {
            $quantity = 1;
        }

        $item = new Item();
        $item->setName($name);
        $item->setQuantity($quantity);
        $item->setShoppingList($list);

        $em->persist($item);
        $em->flush();

        return new JsonResponse($this->serializeItem($item), 201);
    }

    // ---------------------------
    // GET /lists/{id}/items
    // Get all items of a list
    // ---------------------------
    #[Route('/lists/{id}/items', name: 'api_lists_get_items', methods: ['GET'])]
    public function getItems(int $id, EntityManagerInterface $em): JsonResponse
    {
        $list = $em->getRepository(ShoppingList::class)->find($id);
        if (!$list) {
            return new JsonResponse(['error' => 'List not found'], 404);
        }

        $items = [];
        foreach ($list->getItems() as $item) {
            $items[] = $this->serializeItem($item);
        }

        return new JsonResponse($items, 200);
    }

    // ---------------------------
    // GET /lists/{id}/items/{itemId}
    // Get one item (must belong to the list)
    // ---------------------------
    #[Route('/lists/{id}/items/{itemId}', name: 'api_lists_get_item', methods: ['GET'])]
    public function getItem(int $id, int $itemId, EntityManagerInterface $em): JsonResponse
    {
        $list = $em->getRepository(ShoppingList::class)->find($id);
        if (!$list) {
            return new JsonResponse(['error' => 'List not found'], 404);
        }

        $item = $em->getRepository(Item::class)->find($itemId);
        if (!$item) {
            return new JsonResponse(['error' => 'Item not found'], 404);
        }

        if ($item->getShoppingList()?->getId() !== $list->getId()) {
            return new JsonResponse(['error' => 'Item does not belong to this list'], 404);
        }

        return new JsonResponse($this->serializeItem($item), 200);
    }

    // ---------------------------
    // PUT /lists/{id}/items/{itemId}
    // Update an item (must belong to the list)
    // Body: { "name": "Milch", "quantity": 3 }
    // ---------------------------
    #[Route('/lists/{id}/items/{itemId}', name: 'api_lists_update_item', methods: ['PUT'])]
    public function updateItem(int $id, int $itemId, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $list = $em->getRepository(ShoppingList::class)->find($id);
        if (!$list) {
            return new JsonResponse(['error' => 'List not found'], 404);
        }

        $item = $em->getRepository(Item::class)->find($itemId);
        if (!$item) {
            return new JsonResponse(['error' => 'Item not found'], 404);
        }

        if ($item->getShoppingList()?->getId() !== $list->getId()) {
            return new JsonResponse(['error' => 'Item does not belong to this list'], 404);
        }

        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Invalid JSON body'], 400);
        }

        if (array_key_exists('name', $data)) {
            $name = trim((string)$data['name']);
            if ($name === '') {
                return new JsonResponse(['error' => 'Field "name" cannot be empty'], 400);
            }
            $item->setName($name);
        }

        if (array_key_exists('quantity', $data)) {
            $quantity = (int)$data['quantity'];
            if ($quantity < 1) {
                return new JsonResponse(['error' => 'Field "quantity" must be >= 1'], 400);
            }
            $item->setQuantity($quantity);
        }

        $em->flush();

        return new JsonResponse($this->serializeItem($item), 200);
    }

    // ---------------------------
    // DELETE /lists/{id}
    // Delete a list (and its items because of orphanRemoval)
    // ---------------------------
    #[Route('/lists/{id}', name: 'api_lists_delete', methods: ['DELETE'])]
    public function deleteList(int $id, EntityManagerInterface $em): JsonResponse
    {
        $list = $em->getRepository(ShoppingList::class)->find($id);
        if (!$list) {
            return new JsonResponse(['error' => 'List not found'], 404);
        }

        $em->remove($list);
        $em->flush();

        return new JsonResponse(null, 204);
    }

    // ---------------------------
    // DELETE /lists/{id}/items/{itemId}
    // Delete an item (must belong to the list)
    // ---------------------------
    #[Route('/lists/{id}/items/{itemId}', name: 'api_lists_delete_item', methods: ['DELETE'])]
    public function deleteItem(int $id, int $itemId, EntityManagerInterface $em): JsonResponse
    {
        $list = $em->getRepository(ShoppingList::class)->find($id);
        if (!$list) {
            return new JsonResponse(['error' => 'List not found'], 404);
        }

        $item = $em->getRepository(Item::class)->find($itemId);
        if (!$item) {
            return new JsonResponse(['error' => 'Item not found'], 404);
        }

        if ($item->getShoppingList()?->getId() !== $list->getId()) {
            return new JsonResponse(['error' => 'Item does not belong to this list'], 404);
        }

        $em->remove($item);
        $em->flush();

        return new JsonResponse(null, 204);
    }

    // ---------------------------
    // Helpers
    // ---------------------------
    private function serializeItem(Item $item): array
    {
        return [
            'id' => $item->getId(),
            'name' => $item->getName(),
            'quantity' => $item->getQuantity(),
        ];
    }

    private function serializeList(ShoppingList $list): array
    {
        $items = [];
        foreach ($list->getItems() as $item) {
            $items[] = $this->serializeItem($item);
        }

        return [
            'id' => $list->getId(),
            'name' => $list->getName(),
            'items' => $items,
        ];
    }
}