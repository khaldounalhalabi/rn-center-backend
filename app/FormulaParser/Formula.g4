grammar Formula ;

formula: expression EOF;

expression:'(' expression ')'                                                                   #BracedExpression
          | left=expression '%' right=expression                                                #PercentageOperation
          | expression '%'                                                                      #PercentageOfPrevious
          | left=expression op=('*'|'/') right=expression                                       #MulDiv
          | left=expression op=('+'|'-') right=expression                                       #AddSub
          | '-' '(' expression ')'                                                              #NegativeExpression
          | '+' '(' expression ')'                                                              #PositiveExpression
          | 'IF' '('condition=booleanOperations ',' then=expression ',' else=expression ')'     #IFExpression
          | Variable                                                                            #Id
          | IntegerLiteral                                                                      #Int
          | Double                                                                              #Double
          | '-' Variable                                                                        #NegativeId
          | '-' IntegerLiteral                                                                  #NegativeInt
          | '-' Double                                                                          #NegativeDouble
          | '+' Variable                                                                        #PositiveId
          | '+' IntegerLiteral                                                                  #PositiveInt
          | '+' Double                                                                          #PositiveDouble
          ;

booleanOperations:    left=expression    op='<'             right=expression                        #LessThan
                    | left=expression    op='<='            right=expression                        #LessThanOrEqual
                    | left=expression    op='>'             right=expression                        #MoreThan
                    | left=expression    op='>='            right=expression                        #MoreThanOrEqual
                    | left=expression    op=('!='|'<>')     right=expression                        #NotEqual
                    | left=expression    op='='             right=expression                        #IsEqual
                    | 'NOT' '(' booleanOperations (',' booleanOperations)* ')'                      #NotFunction
                    | 'AND' '(' booleanOperations (',' booleanOperations)* ')'                      #AndFunction
                    | 'OR'  '(' booleanOperations (',' booleanOperations)* ')'                      #OrFunction
                    | '(' booleanOperations ')'                                                     #BracedBooleanOperation
                    ;

Variable: [a-zA-Z_] [a-zA-Z_0-9]*;

IntegerLiteral: [0-9]+;

Double: (IntegerLiteral '.' IntegerLiteral) | ('.' IntegerLiteral);

WS: [ \t\r\n]+ -> skip;

// NOTICE :: when adding new lixer rule or any terminal node remember to add its user
// equevilant name to the replaced strings in the \App\Exceptions\FormulaSyntaxException message
// because the parser throws the errors using the names defined here
