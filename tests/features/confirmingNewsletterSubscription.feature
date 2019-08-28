# language: en
Feature: confirming newsletter subscription

  Background: 
    Given a user created a subscription using the "krzysztof.kubacki@blabla.pl" email address and the "test-subscription-id" subscription id

  Scenario: Success confirmation using correct email address and subscription id
    When the user confirms the subscription using the "krzysztof.kubacki@blabla.pl" email address and the "test-subscription-id" subscription id
    Then the subscription should be confirmed

  Scenario: Subscription confirmation fails in case of subscription is already confirmed
    When the user confirms the subscription using the "krzysztof.kubacki@blabla.pl" email address and the "test-subscription-id" subscription id
    Then the subscription should be confirmed
    When the user confirms the subscription using the "krzysztof.kubacki@blabla.pl" email address and the "test-subscription-id" subscription id then confirmation should fail

  Scenario: Subscription confirmation fails in case of confirmation using not existing subscription-id
    When the user confirms the subscription using not existing "not-existing-subscription-id" subscription id then confirmation should fail

  Scenario: Subscription confirmation fails in case of confirmation using not existing email address
    When the user confirms the subscription using not existing "not-existing@email-address.pl" email address then confirmation should fail
