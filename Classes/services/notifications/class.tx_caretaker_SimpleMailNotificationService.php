<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2009-2011 by n@work GmbH and networkteam GmbH
 *
 * All rights reserved
 *
 * This script is part of the Caretaker project. The Caretaker project
 * is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * This is a file of the caretaker project.
 * http://forge.typo3.org/projects/show/extension-caretaker
 *
 * Project sponsored by:
 * n@work GmbH - http://www.work.de
 * networkteam GmbH - http://www.networkteam.com/
 *
 * $Id$
 */

/**
 * A Basic Email Notification-Service
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 * @package TYPO3
 * @subpackage caretaker
 */
class tx_caretaker_SimpleMailNotificationService extends tx_caretaker_AbstractNotificationService
{

    /**
     * The notification storage
     *
     * @var array
     */
    private $recipients_messages = [];

    /**
     * The addresses for the recipients
     *
     * @var array
     */
    private $recipients_addresses = [];

    /**
     * Notification Email from Address
     *
     * @var string
     */
    private $mail_from;

    /**
     * The mail subject Prefix
     *
     * @var string
     */
    private $mail_subject;

    /**
     * URL Pattern for links to display the frontend info for a test
     *
     * @var string URL with a ### marker which is replaced by the caretakerNodId
     */
    private $mail_link;

    /**
     * IDs of roles which are receiving mails
     *
     * @var array
     */
    private $mail_roles = [];

    /**
     * Constructor
     * reads the service configuration
     */
    public function __construct()
    {
        parent::__construct('simple_mail');

        $contactRepository = tx_caretaker_ContactRepository::getInstance();

        $this->mail_from = $this->getConfigValue('mail_from');
        $this->mail_subject = $this->getConfigValue('mail_subject');
        $this->mail_link = $this->getConfigValue('mail_link');
        $this->mail_link = $this->getConfigValue('mail_link');
        $this->mail_roles = [];
        $role_ids = explode(',', $this->getConfigValue('role_ids'));
        foreach ($role_ids as $role_id) {
            $role = $contactRepository->getContactRoleById(trim($role_id));
            if (!$role && is_numeric($role_id)) {
                $role = $contactRepository->getContactRoleByUid(intval($role_id));
            }
            if ($role) {
                $this->mail_roles[] = $role;
            }
        }
    }

    /**
     * Notify the service about a test status
     *
     * @param string $event Event Identifier
     * @param tx_caretaker_AbstractNode $node
     * @param tx_caretaker_TestResult $result
     * @param tx_caretaker_TestResult $lastResult
     */
    public function addNotification($event, $node, $result = null, $lastResult = null)
    {
        // stop if event is not updatedTestResult of a TestNode
        if ($event != 'updatedTestResult' || is_a($node, 'tx_caretaker_TestNode') == false) {
            return;
        }

        // Check that the result is not equal to the previous one
        if ($lastResult && $result->getState() == $lastResult->getState()) {
            return;
        }

        // collect the recipients from the node rootline
        $recipientIds = [];

        if (count($this->mail_roles) > 0) {
            $contacts = [];
            foreach ($this->mail_roles as $role) {
                $contacts = array_merge($contacts, $node->getContacts($role));
            }
        } else {
            $contacts = $node->getContacts();
        }

        /** @var tx_caretaker_Contact $contact */
        foreach ($contacts as $contact) {
            $address = $contact->getAddress();
            if (!$this->recipients_addresses[$address['uid']]) {
                $this->recipients_addresses[$address['uid']] = $address;
            }
            $recipientIds[] = $address['uid'];
        }

        $recipientIds = array_unique($recipientIds);

        // store the notifications for the recipients
        foreach ($recipientIds as $recipientId) {
            if (!isset($this->recipients_messages[$recipientId])) {
                $this->recipients_messages[$recipientId] = [
                    'messages' => [],
                    'num_undefined' => 0,
                    'num_ok' => 0,
                    'num_warning' => 0,
                    'num_error' => 0,
                    'num_ack' => 0,
                    'num_due' => 0,
                ];
            }
            switch ($result->getState()) {
                case tx_caretaker_Constants::state_undefined:
                    $this->recipients_messages[$recipientId]['num_undefined']++;
                    break;
                case tx_caretaker_Constants::state_ok:
                    $this->recipients_messages[$recipientId]['num_ok']++;
                    break;
                case tx_caretaker_Constants::state_warning:
                    $this->recipients_messages[$recipientId]['num_warning']++;
                    break;
                case tx_caretaker_Constants::state_error:
                    $this->recipients_messages[$recipientId]['num_error']++;
                    break;
                case tx_caretaker_Constants::state_ack:
                    $this->recipients_messages[$recipientId]['num_ack']++;
                    break;
                case tx_caretaker_Constants::state_due:
                    $this->recipients_messages[$recipientId]['num_due']++;
                    break;
            }
            array_unshift($this->recipients_messages[$recipientId]['messages'],
                '*' . ($lastResult ? $lastResult->getLocallizedStateInfo() . '->' : '') . $result->getLocallizedStateInfo() . ' ' . $node->getInstance()->getTitle() . ':' . $node->getTitle() . '* ' . $node->getCaretakerNodeId() . chr(10) . chr(10) .
                $result->getLocallizedInfotext() . chr(10) .
                str_replace('###', $node->getCaretakerNodeId(), $this->mail_link) . chr(10)
            );
        }
    }

    /**
     * Send the aggregated Notifications
     */
    public function sendNotifications()
    {
        foreach ($this->recipients_messages as $recipientID => $recipientInfo) {
            $recipient = $this->recipients_addresses[$recipientID];
            if ($recipient && $recipient['email']) {
                $message = implode(chr(10) . chr(10), $recipientInfo['messages']);

                $subject = $this->mail_subject;

                $subject .= ' Statechange: ';

                if ($recipientInfo['num_undefined'] > 0) {
                    $subject .= ' ' . $recipientInfo['num_undefined'] . ' Undefined';
                }
                if ($recipientInfo['num_ok'] > 0) {
                    $subject .= ' ' . $recipientInfo['num_ok'] . ' OK';
                }
                if ($recipientInfo['num_error'] > 0) {
                    $subject .= ' ' . $recipientInfo['num_error'] . ' Errors';
                }
                if ($recipientInfo['num_warning'] > 0) {
                    $subject .= ' ' . $recipientInfo['num_warning'] . ' Warnings';
                }
                if ($recipientInfo['num_ack'] > 0) {
                    $subject .= ' ' . $recipientInfo['num_ack'] . ' Acknowledged';
                }
                if ($recipientInfo['num_due'] > 0) {
                    $subject .= ' ' . $recipientInfo['num_due'] . ' Due';
                }

                $this->sendMail($subject, $recipient['email'], $this->mail_from, $message);
            }
        }
    }

    /**
     * Send a Single Notification Mail
     *
     * @param $subject
     * @param $recipient
     * @param $from
     * @param $message
     * @return int
     */
    private function sendMail($subject, $recipient, $from, $message)
    {
        /** @var \TYPO3\CMS\Core\Mail\MailMessage $mail */
        $mail = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Mail\MailMessage');
        $mail->setFrom($from);
        $mail->setTo($recipient);
        $mail->setSubject($subject);
        $mail->setBody($message);

        return $mail->send();
    }
}
