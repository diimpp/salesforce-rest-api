Salesforce Rest&Bulk API
========================

Experimental Salesforce REST and Bulk API implementation, that designed in fashion of facebook php ads sdk.

Currently supports only bulk requests partially.

## Installation
```bash

composer.phar require diimpp/salesforce-rest-api
```

## Usage
```php
$api = \Diimpp\Salesforce\Api::init($accessToken, $instanceUrl);
// or
// $api = \Diimpp\Salesforce\Api::initWithAuthentication($clientId, $clientSecret, $username, $password);

// Example usage of bulk api query.
$job = new Job($api);
$job->create([
    'operation' => Job::OPERATION_QUERY,
    'object' => 'Contact',
    'contentType' => Job::CONTENT_TYPE_XML
]);

$soql = 'select id, lastname, firstname, salutation, name from Contact';

$jobBatch = new JobBatch($job, $api);
$jobBatch->create(['body' => $soql]);
$job->close();

// Wait till job batch will be completed.
while (JobBatch::STATE_QUEUED === $jobBatch->state || JobBatch::STATE_IN_PROGRESS === $jobBatch->state) {
    $jobBatch->read();
}
if (JobBatch::STATE_COMPLETED !== $jobBatch->state) {
    throw new \RuntimeException(sprintf('Salesforce Job Batch request failed with reason: %s', print_r($jobBatch->getData(), true)));
}

$jobBatchResult = new JobBatchResult($jobBatch);
$jobBatchResult->read();

// Salesforce API returns either ID as string or array of IDs of batch results.
if (is_string($jobBatchResult->result)) {
    $data = $jobBatchResult->retrieveData($jobBatchResult->result));
} elseif (is_array($jobBatchResult->result)) {
    foreach ($jobBatchResult->result as $resultId) {
        $data[] = $jobBatchResult->retrieveData($resultId));
    }
}
```

## Contributions

Patches and use cases are most welcome.
