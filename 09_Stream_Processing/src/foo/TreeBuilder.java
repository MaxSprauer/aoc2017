package foo;

// Copyright 2017 Max Sprauer

import java.io.*;

public class TreeBuilder
{
    public enum State { GARBAGE, NORMAL };
    public State state = State.NORMAL;
    public int garbageCount = 0;

    public TreeBuilder()
    {
    }

    /*
        curNode is the node in the current context.
          { means add a new node below it
          ,{ means add a new sibling next to it
          } means close the group
          }} means go up a level
     */
    public TreeNode getNextNode(TreeNode curNode, Reader r) throws IOException
    {
        int i;
        // TreeNode newNode = null;
        char c;
        char lastChar = ' ';

        while ((i = r.read()) != -1) {
            c = (char) i;

            switch (state) {
                case NORMAL:
                    if (c == '{') {
                        TreeNode parentNode;

                        if (lastChar != '{') {
                            // New sibling group
                            parentNode = (curNode != null) ? curNode.GetParent() : null;
                        } else {
                            // New child group
                            parentNode = curNode;
                        }

                        curNode = new TreeNode(parentNode);
                        if (parentNode != null) {
                            parentNode.AddChild(curNode);
                        }
                        lastChar = c;
                    } else if (c == '}') {
                        // End of a group
                        if (lastChar == '}') {
                            // return curNode;
                            curNode = (curNode == null) ? null : curNode.GetParent();
                        }
                        // otherwise we're done with this group --- do nothing?

                        lastChar = c;
                    } else if (c == ',') {
                        // do nothing
                    } else if (c == '<') {
                        state = state.GARBAGE;
                    } else {
                        curNode.AddChar(c);
                    }

                    if (state != state.GARBAGE) {
                       // lastChar = c;
                    }

                    break;

                case GARBAGE:
                    if (c == '!') {
                        r.skip(1);
                    } else if (c == '>') {
                        state = State.NORMAL;
                        // return null;
                    } else {
                        this.garbageCount++;
                    }

                    // lastChar = ' ';
                    break;
            }
        }

        return curNode;
    }

    public TreeNode build(Reader r) throws java.io.IOException
    {
        TreeNode root = null; // new TreeNode(null);
        root = getNextNode(root, r);
        r.close();
        return root;
    }

    public static int buildFile(String inputFile) throws Exception
    {
        TreeBuilder tb = new TreeBuilder();
        FileReader fr = new FileReader("input.txt");
        TreeNode root = tb.build(fr);
        System.out.println(root.toString() + ": " + root.GetScore(0) + ", Garbage: " + tb.garbageCount);
        return root.GetScore(0);
    }

    public static int buildString(String input) throws Exception
    {
        TreeBuilder tb = new TreeBuilder();
        StringReader r = new StringReader(input);
        TreeNode root = tb.build(r);
        System.out.println(root.toString() + ": " + root.GetScore(0) + ", Garbage: " + tb.garbageCount);
        return root.GetScore(0);
    }

    public static void main(String[] args)
    {
        int s;
        try {

            s = buildString("{}");
            assert 1 == s;
            s = buildString("{{{}}}");
            assert 6 == s;
            s = buildString("{{},{}}");
            assert 5 == s;
            s = buildString("{{{},{},{{}}}}");
            assert 16 == s;
            s = buildString("{<a>,<a>,<a>,<a>}");
            assert 1 == s;
            s = buildString("{{<ab>},{<ab>},{<ab>},{<ab>}}");
            assert 9 == s;
            s = buildString("{{<!!>},{<!!>},{<!!>},{<!!>}}");
            assert 9 == s;
            s = buildString("{{<a!>},{<a!>},{<a!>},{<ab>}}");
            assert 3 == s;
            s = buildString("{{{aaa},<sdad>}}");
            assert 6 == s;

            s = buildFile("input.txt");
            assert 7616 == s;
        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}


