<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/middleware/auth.php';
function collection(string $name): MongoDB\Collection { return getDatabase()->selectCollection($name); }
function asPublicUser(array $user): array { return ['id' => (string) $user['_id'], 'name' => $user['name'], 'email' => $user['email'], 'role' => $user['role']]; }
function serializeDocument(mixed $value): mixed
{
	if ($value instanceof MongoDB\BSON\ObjectId) return (string) $value;
	if ($value instanceof MongoDB\BSON\UTCDateTime) return $value->toDateTime()->format(DATE_ATOM);
	if (is_array($value)) {
		$result = [];
		foreach ($value as $key => $item) $result[$key] = serializeDocument($item);
		return $result;
	}
	return $value;
}