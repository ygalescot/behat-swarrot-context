Feature: Swarrot Context

  Scenario: Test message publication & consumption
    Given I purge queue "test"
    Given I set message properties:
    """
    type: test_type
    headers:
      Content-Type: plain/text
      X-Custom-Header: custom_value
    """
    And I set message body:
    """
    I believe I can fly :)
    """
    And I publish message with routing key "test"
    When I consume a message form queue "test"
    Then print the message body
    And the message body should contain "believe"
    And the message body should be equal to "I believe I can fly :)"
    And print the message properties
    And the message should have property "type" equal to "test_type"
    And the message should have header "Content-Type" equal to "plain/text"
    And the message should have header "X-Custom-Header" equal to "custom_value"

  Scenario: Test JSON message publication & consumption
    Given I purge queue "test"
    Given I set message properties:
    """
    headers:
      Content-Type: application/json
    """
    And I set message body:
    """
    {
      "song": "I believe I can fly"
    }
    """
    And I publish message with routing key "test"
    When I consume a message form queue "test"
    Then print the message body
    And the message body should contain "song"
    And print the message properties
    And the message should have header "Content-Type" equal to "application/json"
    And the message body should have JSON node "song" equal to "I believe I can fly"