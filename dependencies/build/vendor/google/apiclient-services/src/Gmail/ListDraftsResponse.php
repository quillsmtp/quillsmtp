<?php

/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */
namespace QuillSMTP\Vendor\Google\Service\Gmail;

class ListDraftsResponse extends \QuillSMTP\Vendor\Google\Collection
{
    protected $collection_key = 'drafts';
    protected $draftsType = Draft::class;
    protected $draftsDataType = 'array';
    /**
     * @var string
     */
    public $nextPageToken;
    /**
     * @var string
     */
    public $resultSizeEstimate;
    /**
     * @param Draft[]
     */
    public function setDrafts($drafts)
    {
        $this->drafts = $drafts;
    }
    /**
     * @return Draft[]
     */
    public function getDrafts()
    {
        return $this->drafts;
    }
    /**
     * @param string
     */
    public function setNextPageToken($nextPageToken)
    {
        $this->nextPageToken = $nextPageToken;
    }
    /**
     * @return string
     */
    public function getNextPageToken()
    {
        return $this->nextPageToken;
    }
    /**
     * @param string
     */
    public function setResultSizeEstimate($resultSizeEstimate)
    {
        $this->resultSizeEstimate = $resultSizeEstimate;
    }
    /**
     * @return string
     */
    public function getResultSizeEstimate()
    {
        return $this->resultSizeEstimate;
    }
}
// Adding a class alias for backwards compatibility with the previous class name.
\class_alias(ListDraftsResponse::class, 'QuillSMTP\\Vendor\\Google_Service_Gmail_ListDraftsResponse');
