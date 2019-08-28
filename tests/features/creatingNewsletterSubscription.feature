# language: en
Feature: Creating a newsletter subscription

  Scenario: Success when creating a newsletter subscription
    Given a user has the "krzysztof.kubacki@blabla.pl" email address
    When the user subscribe in a newsletter
    Then a new newsletter subscription should be created

  Scenario: Creating a newsletter subscription fails when subscription with email address already exists
    Given a user has the "krzysztof.kubacki@blabla.pl" email address
    And the user has an unconfirmed subscription already
    When the user subscribe in a newsletter with the same email address the subscription should fail
    